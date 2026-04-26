/**
 * Offensive Security Dashboard - Educational Security Tools Interface
 * Laboratory use only - For authorized testing purposes
 */

'use client';

import { useState, useEffect } from 'react';
import { offensiveSecurityService, NetworkScanOptions, OSINTOptions } from '@/lib/offensive-security-service';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Loader2, Shield, Globe, Search, Bug, Network, Eye, AlertTriangle } from 'lucide-react';

interface ScanResult {
  target: string;
  scanType: string;
  results: any[];
  timestamp: string;
  status: 'running' | 'completed' | 'error';
}

export default function OffensiveSecurityDashboard() {
  const [activeTab, setActiveTab] = useState('network');
  const [target, setTarget] = useState('');
  const [scanType, setScanType] = useState('basic');
  const [targetType, setTargetType] = useState('domain');
  const [ports, setPorts] = useState('common');
  const [isScanning, setIsScanning] = useState(false);
  const [scanResults, setScanResults] = useState<ScanResult[]>([]);
  const [error, setError] = useState<string | null>(null);

  const handleNetworkScan = async () => {
    if (!target) {
      setError('Please enter a target');
      return;
    }

    setIsScanning(true);
    setError(null);

    try {
      const options: NetworkScanOptions = {
        target,
        ports,
        scanType: scanType as any,
        timeout: 5
      };

      const result = await offensiveSecurityService.networkScanner(options);
      setScanResults(prev => [result, ...prev]);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Scan failed');
    } finally {
      setIsScanning(false);
    }
  };

  const handleOSINTScan = async () => {
    if (!target) {
      setError('Please enter a target');
      return;
    }

    setIsScanning(true);
    setError(null);

    try {
      const options: OSINTOptions = {
        target,
        type: targetType as any,
        depth: 'basic'
      };

      const result = await offensiveSecurityService.osintScanner(options);
      setScanResults(prev => [result, ...prev]);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'OSINT scan failed');
    } finally {
      setIsScanning(false);
    }
  };

  const handleVulnerabilityScan = async () => {
    if (!target) {
      setError('Please enter a target');
      return;
    }

    setIsScanning(true);
    setError(null);

    try {
      const result = await offensiveSecurityService.vulnerabilityScanner(target);
      setScanResults(prev => [result, ...prev]);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Vulnerability scan failed');
    } finally {
      setIsScanning(false);
    }
  };

  const handleCoordinatedScan = async () => {
    if (!target) {
      setError('Please enter a target');
      return;
    }

    setIsScanning(true);
    setError(null);

    try {
      const results = await offensiveSecurityService.runCoordinatedScan(target, ['network', 'osint', 'vulnerability']);
      setScanResults(prev => [...results, ...prev]);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Coordinated scan failed');
    } finally {
      setIsScanning(false);
    }
  };

  const getSeverityColor = (severity: string) => {
    switch (severity?.toLowerCase()) {
      case 'high': return 'destructive';
      case 'medium': return 'warning';
      case 'low': return 'secondary';
      default: return 'default';
    }
  };

  const renderResult = (result: any, index: number) => {
    if (result.type === 'dns_a') {
      return (
        <div key={index} className="flex items-center space-x-2 p-2 bg-blue-50 rounded">
          <Network className="h-4 w-4 text-blue-600" />
          <span className="text-sm">A Record: {result.value}</span>
        </div>
      );
    }
    
    if (result.type === 'port') {
      return (
        <div key={index} className="flex items-center space-x-2 p-2 bg-green-50 rounded">
          <Shield className="h-4 w-4 text-green-600" />
          <span className="text-sm">Port {result.port} ({result.service}) - {result.status}</span>
        </div>
      );
    }
    
    if (result.type === 'missing_security_headers') {
      return (
        <div key={index} className="flex items-center space-x-2 p-2 bg-yellow-50 rounded">
          <AlertTriangle className="h-4 w-4 text-yellow-600" />
          <span className="text-sm">Missing Headers: {result.headers.join(', ')}</span>
          <Badge variant={getSeverityColor(result.severity)}>{result.severity}</Badge>
        </div>
      );
    }
    
    if (result.error) {
      return (
        <div key={index} className="flex items-center space-x-2 p-2 bg-red-50 rounded">
          <Bug className="h-4 w-4 text-red-600" />
          <span className="text-sm text-red-700">Error: {result.error}</span>
        </div>
      );
    }

    return (
      <div key={index} className="p-2 bg-gray-50 rounded text-sm">
        <pre className="text-xs">{JSON.stringify(result, null, 2)}</pre>
      </div>
    );
  };

  return (
    <div className="container mx-auto p-6 space-y-6">
      <div className="flex items-center space-x-3">
        <Shield className="h-8 w-8 text-red-600" />
        <div>
          <h1 className="text-3xl font-bold text-gray-900">Offensive Security Dashboard</h1>
          <p className="text-gray-600">Educational security tools for authorized testing only</p>
        </div>
      </div>

      {error && (
        <Alert variant="destructive">
          <AlertTriangle className="h-4 w-4" />
          <AlertDescription>{error}</AlertDescription>
        </Alert>
      )}

      <Card>
        <CardHeader>
          <CardTitle>Security Scanning Tools</CardTitle>
          <CardDescription>
            Perform authorized security testing and reconnaissance
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Tabs value={activeTab} onValueChange={setActiveTab}>
            <TabsList className="grid w-full grid-cols-4">
              <TabsTrigger value="network">
                <Network className="h-4 w-4 mr-2" />
                Network
              </TabsTrigger>
              <TabsTrigger value="osint">
                <Eye className="h-4 w-4 mr-2" />
                OSINT
              </TabsTrigger>
              <TabsTrigger value="vulnerability">
                <Bug className="h-4 w-4 mr-2" />
                Vulnerability
              </TabsTrigger>
              <TabsTrigger value="coordinated">
                <Search className="h-4 w-4 mr-2" />
                Coordinated
              </TabsTrigger>
            </TabsList>

            <div className="mt-6 space-y-4">
              <div className="flex space-x-4">
                <Input
                  placeholder="Enter target (domain.com or IP)"
                  value={target}
                  onChange={(e) => setTarget(e.target.value)}
                  className="flex-1"
                />
                
                {activeTab === 'network' && (
                  <Select value={ports} onValueChange={setPorts}>
                    <SelectTrigger className="w-32">
                      <SelectValue placeholder="Ports" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="common">Common</SelectItem>
                      <SelectItem value="1-1000">1-1000</SelectItem>
                      <SelectItem value="80,443,8080">Web</SelectItem>
                      <SelectItem value="21,22,23,25">Network</SelectItem>
                    </SelectContent>
                  </Select>
                )}
                
                {activeTab === 'osint' && (
                  <Select value={targetType} onValueChange={setTargetType}>
                    <SelectTrigger className="w-32">
                      <SelectValue placeholder="Type" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="domain">Domain</SelectItem>
                      <SelectItem value="ip">IP Address</SelectItem>
                      <SelectItem value="email">Email</SelectItem>
                      <SelectItem value="subdomain">Subdomain</SelectItem>
                    </SelectContent>
                  </Select>
                )}
              </div>

              <div className="flex space-x-2">
                {activeTab === 'network' && (
                  <Button 
                    onClick={handleNetworkScan} 
                    disabled={isScanning}
                    className="bg-blue-600 hover:bg-blue-700"
                  >
                    {isScanning ? (
                      <Loader2 className="h-4 w-4 animate-spin mr-2" />
                    ) : (
                      <Network className="h-4 w-4 mr-2" />
                    )}
                    Scan Network
                  </Button>
                )}
                
                {activeTab === 'osint' && (
                  <Button 
                    onClick={handleOSINTScan} 
                    disabled={isScanning}
                    className="bg-green-600 hover:bg-green-700"
                  >
                    {isScanning ? (
                      <Loader2 className="h-4 w-4 animate-spin mr-2" />
                    ) : (
                      <Eye className="h-4 w-4 mr-2" />
                    )}
                    OSINT Scan
                  </Button>
                )}
                
                {activeTab === 'vulnerability' && (
                  <Button 
                    onClick={handleVulnerabilityScan} 
                    disabled={isScanning}
                    className="bg-red-600 hover:bg-red-700"
                  >
                    {isScanning ? (
                      <Loader2 className="h-4 w-4 animate-spin mr-2" />
                    ) : (
                      <Bug className="h-4 w-4 mr-2" />
                    )}
                    Vulnerability Scan
                  </Button>
                )}
                
                {activeTab === 'coordinated' && (
                  <Button 
                    onClick={handleCoordinatedScan} 
                    disabled={isScanning}
                    className="bg-purple-600 hover:bg-purple-700"
                  >
                    {isScanning ? (
                      <Loader2 className="h-4 w-4 animate-spin mr-2" />
                    ) : (
                      <Search className="h-4 w-4 mr-2" />
                    )}
                    Run All Scans
                  </Button>
                )}
              </div>
            </div>
          </Tabs>
        </CardContent>
      </Card>

      {scanResults.length > 0 && (
        <Card>
          <CardHeader>
            <CardTitle>Scan Results</CardTitle>
            <CardDescription>
              Results from recent security scans
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4 max-h-96 overflow-y-auto">
              {scanResults.map((scan, scanIndex) => (
                <div key={scanIndex} className="border rounded-lg p-4">
                  <div className="flex justify-between items-center mb-2">
                    <div className="flex items-center space-x-2">
                      <Badge variant="outline">{scan.scanType}</Badge>
                      <span className="font-medium">{scan.target}</span>
                    </div>
                    <Badge 
                      variant={scan.status === 'completed' ? 'default' : 
                              scan.status === 'error' ? 'destructive' : 'secondary'}
                    >
                      {scan.status}
                    </Badge>
                  </div>
                  
                  <div className="text-xs text-gray-500 mb-2">
                    {new Date(scan.timestamp).toLocaleString()}
                  </div>
                  
                  <div className="space-y-2">
                    {scan.results.map((result, resultIndex) => renderResult(result, resultIndex))}
                  </div>
                  
                  {scan.results.length === 0 && (
                    <div className="text-sm text-gray-500 italic">
                      No results found
                    </div>
                  )}
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      )}

      <Alert>
        <AlertTriangle className="h-4 w-4" />
        <AlertDescription>
          <strong>Legal Notice:</strong> These tools are for educational and authorized testing purposes only. 
          Always obtain proper authorization before scanning any systems you don't own. Unauthorized use is illegal.
        </AlertDescription>
      </Alert>
    </div>
  );
}