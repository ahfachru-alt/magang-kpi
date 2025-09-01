<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\QuantumComputingService;
use Illuminate\Support\Facades\Log;

class QuantumComputing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quantum:compute {circuit? : Quantum circuit to run} {--all : Run all quantum circuits} {--status : Show quantum system status}';
    protected $description = 'Run quantum computing simulations for CCTV monitoring system';

    protected $quantumService;

    public function __construct(QuantumComputingService $quantumService)
    {
        parent::__construct();
        $this->quantumService = $quantumService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('⚛️ QUANTUM COMPUTING SIMULATION STARTED');
        $this->newLine();

        if ($this->option('status')) {
            $this->showQuantumSystemStatus();
        } elseif ($this->option('all')) {
            $this->runAllQuantumSimulations();
        } elseif ($this->argument('circuit')) {
            $circuitName = $this->argument('circuit');
            $this->runQuantumSimulation($circuitName);
        } else {
            $this->showQuantumSystemStatus();
        }

        $this->newLine();
        $this->info('✅ QUANTUM COMPUTING SIMULATION COMPLETED');
    }

    protected function showQuantumSystemStatus()
    {
        $this->info('🔍 Quantum System Status:');
        
        try {
            $status = $this->quantumService->getQuantumSystemStatus();
            
            $this->table(
                ['Metric', 'Value'],
                [
                    ['System Health', round($status['system_health'], 2) . '%'],
                    ['Quantum Coherence', round($status['quantum_coherence'], 2) . '%'],
                    ['Available Qubits', implode(', ', $status['qubits'])],
                    ['Available Gates', implode(', ', array_keys($status['available_gates']))],
                    ['Available Circuits', implode(', ', $status['available_circuits'])],
                ]
            );

        } catch (\Exception $e) {
            $this->error('❌ Failed to get quantum system status: ' . $e->getMessage());
        }
    }

    protected function runQuantumSimulation(string $circuitName)
    {
        $this->info("🚀 Running Quantum Simulation: {$circuitName}");
        
        try {
            $result = $this->quantumService->runQuantumSimulation($circuitName);
            
            if ($result['success']) {
                $this->displayQuantumResults($result);
            } else {
                $this->error("❌ Quantum simulation failed: " . $result['error']);
            }

        } catch (\Exception $e) {
            $this->error("❌ Quantum simulation failed: " . $e->getMessage());
            Log::error("Quantum simulation failed: " . $e->getMessage());
        }
    }

    protected function runAllQuantumSimulations()
    {
        $this->info('🚀 Running All Quantum Simulations...');
        
        try {
            $results = $this->quantumService->runAllQuantumSimulations();
            
            if ($results['success']) {
                $this->displayAllQuantumResults($results);
            } else {
                $this->error('❌ Failed to run all quantum simulations');
            }

        } catch (\Exception $e) {
            $this->error('❌ Failed to run all quantum simulations: ' . $e->getMessage());
            Log::error("All quantum simulations failed: " . $e->getMessage());
        }
    }

    protected function displayQuantumResults(array $result)
    {
        $this->info('📊 Quantum Simulation Results:');
        $this->newLine();

        // Circuit Information
        $this->line("🔧 Circuit: <fg=cyan>{$result['circuit']}</>");
        $this->line("⏱️ Execution Time: <fg=green>{$result['execution_time']}s</>");
        $this->newLine();

        // Quantum Advantage
        if (isset($result['results']['quantum_advantage'])) {
            $advantage = $result['results']['quantum_advantage'];
            $this->line("⚡ Quantum Advantage Analysis:");
            $this->line("   Speedup Factor: <fg=yellow>{$advantage['speedup_factor']}x</>");
            $this->line("   Efficiency Gain: <fg=green>{$advantage['efficiency_gain']}%</>");
            $this->line("   Quantum Superiority: " . ($advantage['quantum_superiority'] ? '✅ Yes' : '❌ No'));
        }
        $this->newLine();

        // Circuit Details
        $this->info('🔬 Circuit Details:');
        $this->line("   Qubits Used: <fg=cyan>{$result['results']['qubit_count']}</>");
        $this->line("   Gates Applied: <fg=cyan>{$result['results']['circuit_complexity']}</>");
        $this->newLine();

        // Quantum Properties
        $this->displayQuantumProperties($result['results']['quantum_properties']);
    }

    protected function displayAllQuantumResults(array $results)
    {
        $this->info('📊 All Quantum Simulation Results:');
        $this->newLine();

        $overallAdvantage = $results['overall_quantum_advantage'];
        $this->info('🏆 Overall Quantum Advantage:');
        $this->line("   Average Speedup: <fg=yellow>{$overallAdvantage['average_speedup']}x</>");
        $this->line("   Overall Advantage: <fg=yellow>{$overallAdvantage['overall_advantage']}</>");
        $this->line("   Successful Circuits: <fg=green>{$overallAdvantage['successful_circuits']}/{$overallAdvantage['total_circuits']}</>");
        $this->newLine();

        // Individual Circuit Results
        foreach ($results['results'] as $circuitName => $result) {
            if ($result['success']) {
                $this->line("✅ <fg=green>{$circuitName}</> - Completed");
                $advantage = $result['results']['quantum_advantage'];
                $this->line("   Speedup: {$advantage['speedup_factor']}x | Gain: {$advantage['efficiency_gain']}%");
            } else {
                $this->line("❌ <fg=red>{$circuitName}</> - Failed: " . $result['error']);
            }
        }
    }

    protected function displayQuantumProperties(array $properties)
    {
        $this->info('⚛️ Quantum Properties:');
        
        foreach ($properties as $qubit => $property) {
            $this->line("🔹 <fg=cyan>{$qubit}</> Qubit:");
            $this->line("   Superposition: " . ($property['superposition'] ? '✅ Yes' : '❌ No'));
            $this->line("   Entanglement: <fg=yellow>" . round($property['entanglement'], 4) . "</>");
            $this->line("   Coherence: <fg=green>" . round($property['coherence'], 4) . "</>");
            
            $stats = $property['measurement_statistics'];
            $this->line("   |0⟩ Probability: <fg=blue>{$stats['|0⟩_probability']}%</>");
            $this->line("   |1⟩ Probability: <fg=blue>{$stats['|1⟩_probability']}%</>");
            $this->line("   Total Measurements: <fg=blue>{$stats['total_measurements']}</>");
            $this->newLine();
        }
    }

    protected function displayQuantumGates()
    {
        $this->info('🔧 Available Quantum Gates:');
        
        $gates = [
            ['Gate', 'Description', 'Matrix'],
            ['H', 'Hadamard', '1/√2 [[1,1],[1,-1]]'],
            ['X', 'Pauli-X', '[[0,1],[1,0]]'],
            ['Y', 'Pauli-Y', '[[0,-i],[i,0]]'],
            ['Z', 'Pauli-Z', '[[1,0],[0,-1]]'],
            ['CNOT', 'Controlled-NOT', '[[1,0,0,0],[0,1,0,0],[0,0,0,1],[0,0,1,0]]'],
            ['SWAP', 'Swap', '[[1,0,0,0],[0,0,1,0],[0,1,0,0],[0,0,0,1]]'],
        ];

        $this->table($gates[0], array_slice($gates, 1));
    }

    protected function displayQuantumCircuits()
    {
        $this->info('🔬 Available Quantum Circuits:');
        
        $circuits = [
            ['Circuit', 'Qubits', 'Gates', 'Measurements', 'Description'],
            ['anomaly_detection', '2', '4', '1000', 'Anomaly detection in CCTV monitoring'],
            ['security_analysis', '2', '5', '1000', 'Security threat analysis'],
            ['performance_optimization', '2', '4', '1000', 'System performance optimization'],
            ['predictive_modeling', '3', '5', '1000', 'Predictive modeling and forecasting'],
        ];

        $this->table($circuits[0], array_slice($circuits, 1));
    }
}
