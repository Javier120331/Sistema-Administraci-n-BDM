<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Empleado;
use App\Trabajador;
use App\Vacacion;
use App\Licencia;
use App\MovimientoAsistencia;
use App\Periodo;
use App\Finiquito;
use App\SueldoBase;
use App\CargoEmpleado;
use DB;
use Response;

class BuscadorTrabajadoresController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra la página principal del buscador
     */
    public function index()
    {
        return view('buscador.index');
    }

    /**
     * Busca trabajadores según los criterios proporcionados
     */
    public function buscar(Request $request)
    {
        $termino = $request->input('termino');
        $tipo = $request->input('tipo', 'todos'); // todos, empleados, trabajadores
        
        if (empty($termino)) {
            return Response::json([
                'success' => false,
                'message' => 'Debe ingresar un término de búsqueda',
                'data' => []
            ]);
        }

        $resultados = [];

        try {
            // Buscar en Empleados (tabla local - suelditas)
            if ($tipo === 'todos' || $tipo === 'empleados') {
                $empleados = Empleado::where(function($query) use ($termino) {
                    $query->where('rut', 'LIKE', "%{$termino}%")
                          ->orWhere('nombre', 'LIKE', "%{$termino}%");
                })
                ->with(['area', 'comuna'])
                ->get();

                foreach ($empleados as $empleado) {
                    $resultados[] = [
                        'id' => $empleado->id,
                        'rut' => $empleado->rut,
                        'nombre' => $empleado->nombre,
                        'email' => 'N/A',
                        'area' => $empleado->area ? $empleado->area->nombre : 'N/A',
                        'comuna' => $empleado->comuna ? $empleado->comuna->nombre : 'N/A',
                        'direccion' => $empleado->direccion ?? 'N/A',
                        'fecha_ingreso' => $empleado->fecha_inicio_contrato ? $empleado->fecha_inicio_contrato->format('d/m/Y') : 'N/A',
                        'tipo' => 'Empleado'
                    ];
                }
            }

            // Buscar en Trabajadores (tabla Veritas)
            if ($tipo === 'todos' || $tipo === 'trabajadores') {
                $trabajadores = Trabajador::on('veritas')
                    ->where(function($query) use ($termino) {
                        $query->where('rut', 'LIKE', "%{$termino}%")
                              ->orWhere('nombre', 'LIKE', "%{$termino}%");
                    })
                    ->where('estado', 1)
                    ->limit(50)
                    ->get();

                foreach ($trabajadores as $trabajador) {
                    $resultados[] = [
                        'id' => $trabajador->id,
                        'rut' => $trabajador->rut,
                        'nombre' => $trabajador->nombre,
                        'email' => 'N/A',
                        'area' => 'Trabajador (Veritas)',
                        'comuna' => 'N/A',
                        'direccion' => 'N/A',
                        'fecha_ingreso' => 'N/A',
                        'tipo' => 'Trabajador'
                    ];
                }
            }

            return Response::json([
                'success' => true,
                'message' => count($resultados) . ' resultado(s) encontrado(s)',
                'data' => $resultados
            ]);

        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Error al buscar: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * Muestra el dashboard detallado de un trabajador
     */
    public function detalle($tipo, $id)
    {
        try {
            if ($tipo === 'empleado') {
                $trabajador = Empleado::with([
                    'area', 
                    'comuna',
                    'periodos', // Agregar periodos para las vacaciones
                    'licencias' => function($query) {
                        $query->with('causa')->orderBy('fecha_inicio', 'desc')->limit(10);
                    },
                    'finiquitos' => function($query) {
                        $query->orderBy('created_at', 'desc')->limit(5);
                    },
                    'movimientosAsistencias' => function($query) {
                        $query->orderBy('fecha', 'desc')->limit(20);
                    },
                    'sueldosBase' => function($query) {
                        $query->orderBy('created_at', 'desc')->limit(5);
                    },
                    'cargosEmpleado' => function($query) {
                        $query->with('cargo')->orderBy('created_at', 'desc');
                    },
                    'bonos' => function($query) {
                        $query->with('tipoBono')->orderBy('created_at', 'desc')->limit(10);
                    }
                ])->findOrFail($id);

                // Obtener vacaciones a través de periodos (si existen)
                $vacaciones = collect(); // Colección vacía por defecto
                
                // Intentar obtener vacaciones si hay periodos
                if ($trabajador->periodos && $trabajador->periodos->count() > 0) {
                    $periodosIds = $trabajador->periodos->pluck('id');
                    $vacaciones = Vacacion::whereIn('periodo_id', $periodosIds)
                        ->orderBy('fecha_inicio', 'desc')
                        ->limit(10)
                        ->get();
                }

                // Calcular estadísticas
                $stats = [
                    'total_licencias' => Licencia::where('empleado_id', $id)->count(),
                    'total_vacaciones' => $trabajador->periodos ? 
                        Vacacion::whereIn('periodo_id', $trabajador->periodos->pluck('id'))->count() : 0,
                    'dias_vacaciones_pendientes' => 0, // Simplificado por ahora
                    'total_movimientos' => MovimientoAsistencia::where('empleado_id', $id)->count(),
                    'cargo_actual' => $trabajador->getCargoActual()->first(),
                    'sueldo_actual' => $trabajador->sueldosBase()->orderBy('created_at', 'desc')->first(),
                ];

                return view('buscador.detalle_empleado', compact('trabajador', 'vacaciones', 'stats', 'tipo'));

            } else if ($tipo === 'trabajador') {
                // Para trabajadores de Veritas
                $trabajador = Trabajador::on('veritas')->findOrFail($id);
                
                // Buscar producciones del trabajador
                $producciones = DB::connection('veritas')
                    ->table('producciones')
                    ->where('cosechador_rut', $trabajador->rut)
                    ->orderBy('fecha', 'desc')
                    ->limit(20)
                    ->get();

                $stats = [
                    'total_producciones' => DB::connection('veritas')
                        ->table('producciones')
                        ->where('cosechador_rut', $trabajador->rut)
                        ->count(),
                    'produccion_total_kilos' => DB::connection('veritas')
                        ->table('producciones')
                        ->where('cosechador_rut', $trabajador->rut)
                        ->sum('kilos'),
                ];

                return view('buscador.detalle_trabajador', compact('trabajador', 'producciones', 'stats', 'tipo'));
            }

        } catch (\Exception $e) {
            // Mostrar el error completo para debug
            dd([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Calcula días de vacaciones pendientes
     */
    private function calcularDiasVacacionesPendientes($empleado_id)
    {
        $vacaciones = Vacacion::where('empleado_id', $empleado_id)->get();
        $diasTotales = 0;
        $diasTomados = 0;

        foreach ($vacaciones as $vacacion) {
            // Usar cantidad_dias directamente si existe
            if (isset($vacacion->cantidad_dias)) {
                $diasTomados += $vacacion->cantidad_dias;
            } else if ($vacacion->fecha_inicio && $vacacion->fecha_termino) {
                // Calcular días entre fecha_inicio y fecha_termino
                $dias = $vacacion->fecha_inicio->diffInDays($vacacion->fecha_termino) + 1;
                $diasTomados += $dias;
            }
        }

        // Asumir 15 días por año (esto puede variar según la lógica del negocio)
        return max(0, 15 - $diasTomados);
    }
}
