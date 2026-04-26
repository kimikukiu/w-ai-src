/**
 * Email OSINT Tool - Educational email intelligence gathering
 * Based on OSINT concepts for authorized security testing
 */

'use client';

import { useState } from 'react';
import { emailOSINTService, EmailValidationResult, EmailOSINTResult } from '@/lib/email-osint-service';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Loader2, Mail, Shield, Globe, AlertTriangle, CheckCircle, XCircle } from 'lucide-react';

interface ValidationDisplayProps {
  result: EmailValidationResult;
}

function ValidationDisplay({ result }: ValidationDisplayProps) {
  const getRiskColor = (risk: string) => {
    switch (risk) {
      case 'low': return 'bg-green-100 text-green-800';
      case 'medium': return 'bg-yellow-100 text-yellow-800';
      case 'high': return 'bg-red-100 text-red-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-2">
          <Mail className="h-5 w-5 text-blue-600" />
          <span className="font-medium">{result.email}</span>
        </div>
        <Badge className={getRiskColor(result.riskLevel)}>
          {result.riskLevel.toUpperCase()} RISK
        </Badge>
      </div>

      <div className="grid grid-cols-2 gap-4">
        <div className="flex items-center space-x-2">
          {result.format ? (
            <CheckCircle className="h-4 w-4 text-green-600" />
          ) : (
            <XCircle className="h-4 w-4 text-red-600" />
          )}
          <span className="text-sm">Format Valid</span>
        </div>

        <div className="flex items-center space-x-2">
          {result.domainExists ? (
            <CheckCircle className="h-4 w-4 text-green-600" />
          ) : (
            <XCircle className="h-4 w-4 text-red-600" />
          )}
          <span className="text-sm">Domain Exists</span>
        </div>

        <div className="flex items-center space-x-2">
          {result.hasMXRecords ? (
            <CheckCircle className="h-4 w-4 text-green-600" />
          ) : (
            <XCircle className="h-4 w-4 text-red-600" />
          )}
          <span className="text-sm">MX Records</span>
        </div>

        <div className="flex items-center space-x-2">
          {result.isValid ? (
            <CheckCircle className="h-4 w-4 text-green-600" />
          ) : (
            <XCircle className="h-4 w-4 text-red-600" />
          )}
          <span className="text-sm">Valid Email</span>
        </div>
      </div>

      {result.domain && (
        <div className="flex items-center space-x-2">
          <Globe className="h-4 w-4 text-blue-600" />
          <span className="text-sm text-gray-600">Domain: {result.domain}</span>
        </div>
      )}
    </div>
  );
}

export default function EmailOSINTTool() {
  const [email, setEmail] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [validationResult, setValidationResult] = useState<EmailValidationResult | null>(null);
  const [domainResult, setDomainResult] = useState<EmailOSINTResult | null>(null);
  const [breachResult, setBreachResult] = useState<EmailOSINTResult | null>(null);
  const [error, setError] = useState<string | null>(null);

  const handleValidate = async () => {
    if (!email) {
      setError('Please enter an email address');
      return;
    }

    setIsLoading(true);
    setError(null);

    try {
      const result = await emailOSINTService.validateEmail(email);
      setValidationResult(result);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Validation failed');
    } finally {
      setIsLoading(false);
    }
  };

  const handleDomainAnalysis = async () => {
    if (!email) {
      setError('Please enter an email address');
      return;
    }

    setIsLoading(true);
    setError(null);

    try {
      const result = await emailOSINTService.analyzeEmailDomain(email);
      setDomainResult(result);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Domain analysis failed');
    } finally {
      setIsLoading(false);
    }
  };

  const handleBreachCheck = async () => {
    if (!email) {
      setError('Please enter an email address');
      return;
    }

    setIsLoading(true);
    setError(null);

    try {
      const result = await emailOSINTService.checkBreachStatus(email);
      setBreachResult(result);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Breach check failed');
    } finally {
      setIsLoading(false);
    }
  };

  const handleCompleteAnalysis = async () => {
    if (!email) {
      setError('Please enter an email address');
      return;
    }

    setIsLoading(true);
    setError(null);

    try {
      const results = await emailOSINTService.runCompleteEmailOSINT(email);
      setValidationResult(results.validation);
      setDomainResult(results.domainAnalysis);
      setBreachResult(results.breachCheck);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Complete analysis failed');
    } finally {
      setIsLoading(false);
    }
  };

  const renderBreachResult = (result: any) => {
    if (result.type === 'breach_found') {
      return (
        <div className="p-3 bg-red-50 border border-red-200 rounded-lg">
          <div className="flex items-center space-x-2 mb-2">
            <AlertTriangle className="h-4 w-4 text-red-600" />
            <span className="font-medium text-red-800">Breach Found: {result.breach_name}</span>
          </div>
          <p className="text-sm text-red-700 mb-1">Date: {result.breach_date}</p>
          <p className="text-sm text-red-700 mb-1">Affected accounts: {result.affected_accounts.toLocaleString()}</p>
          <p className="text-sm text-red-700 mb-2">Data exposed: {result.data_types_exposed.join(', ')}</p>
          <p className="text-sm text-red-800 font-medium">Recommendation: {result.recommendation}</p>
        </div>
      );
    }
    
    if (result.type === 'no_known_breaches') {
      return (
        <div className="p-3 bg-green-50 border border-green-200 rounded-lg">
          <div className="flex items-center space-x-2">
            <CheckCircle className="h-4 w-4 text-green-600" />
            <span className="font-medium text-green-800">{result.message}</span>
          </div>
          <p className="text-sm text-green-700 mt-1">{result.recommendation}</p>
        </div>
      );
    }

    return (
      <div className="p-3 bg-gray-50 border border-gray-200 rounded-lg">
        <pre className="text-xs">{JSON.stringify(result, null, 2)}</pre>
      </div>
    );
  };

  return (
    <div className="container mx-auto p-6 space-y-6">
      <div className="flex items-center space-x-3">
        <Mail className="h-8 w-8 text-blue-600" />
        <div>
          <h1 className="text-3xl font-bold text-gray-900">Email OSINT Tool</h1>
          <p className="text-gray-600">Educational email intelligence gathering and validation</p>
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
          <CardTitle>Email Analysis</CardTitle>
          <CardDescription>
            Enter an email address to perform OSINT analysis
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            <div className="flex space-x-2">
              <Input
                type="email"
                placeholder="Enter email address (e.g., user@example.com)"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="flex-1"
              />
              <Button 
                onClick={handleCompleteAnalysis} 
                disabled={isLoading}
                className="bg-blue-600 hover:bg-blue-700"
              >
                {isLoading ? (
                  <Loader2 className="h-4 w-4 animate-spin" />
                ) : (
                  <Shield className="h-4 w-4" />
                )}
                Analyze
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>

      <Tabs defaultValue="validation" className="space-y-4">
        <TabsList className="grid w-full grid-cols-3">
          <TabsTrigger value="validation">Validation</TabsTrigger>
          <TabsTrigger value="domain">Domain Analysis</TabsTrigger>
          <TabsTrigger value="breach">Breach Check</TabsTrigger>
        </TabsList>

        <TabsContent value="validation">
          <Card>
            <CardHeader>
              <CardTitle>Email Validation</CardTitle>
              <CardDescription>
                Check email format, domain existence, and MX records
              </CardDescription>
            </CardHeader>
            <CardContent>
              {validationResult ? (
                <ValidationDisplay result={validationResult} />
              ) : (
                <div className="text-center py-8">
                  <Mail className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                  <p className="text-gray-500">Enter an email and click Analyze to see validation results</p>
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="domain">
          <Card>
            <CardHeader>
              <CardTitle>Domain Intelligence</CardTitle>
              <CardDescription>
                Analyze email domain DNS records and configuration
              </CardDescription>
            </CardHeader>
            <CardContent>
              {domainResult ? (
                <div className="space-y-3">
                  {domainResult.results.map((result, index) => (
                    <div key={index} className="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                      <div className="flex items-center space-x-2 mb-1">
                        <Globe className="h-4 w-4 text-blue-600" />
                        <span className="font-medium text-blue-800 capitalize">
                          {result.type.replace('_', ' ')}
                        </span>
                      </div>
                      <pre className="text-xs text-blue-700">
                        {JSON.stringify(result, null, 2)}
                      </pre>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="text-center py-8">
                  <Globe className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                  <p className="text-gray-500">Enter an email and click Analyze to see domain analysis</p>
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="breach">
          <Card>
            <CardHeader>
              <CardTitle>Security Breach Check</CardTitle>
              <CardDescription>
                Check if email appears in known security breaches (simulated data)
              </CardDescription>
            </CardHeader>
            <CardContent>
              {breachResult ? (
                <div className="space-y-3">
                  {breachResult.results.map((result, index) => renderBreachResult(result))}
                </div>
              ) : (
                <div className="text-center py-8">
                  <Shield className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                  <p className="text-gray-500">Enter an email and click Analyze to check breach status</p>
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>

      <Alert>
        <AlertTriangle className="h-4 w-4" />
        <AlertDescription>
          <strong>Legal Notice:</strong> This tool is for educational and authorized testing purposes only. 
          Always ensure you have permission to analyze email addresses. Unauthorized use may violate privacy laws.
        </AlertDescription>
      </Alert>
    </div>
  );
}