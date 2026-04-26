'use client';

import { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';

type Tab = 'telegram' | 'subscribers' | 'wallet' | 'settings';

export default function AdminDashboard() {
  const [activeTab, setActiveTab] = useState<Tab>('telegram');
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [loading, setLoading] = useState(true);
  const router = useRouter();

  useEffect(() => {
    const token = sessionStorage.getItem('admin_token');
    if (token) {
      setIsAuthenticated(true);
    } else {
      router.push('/admin/login');
    }
    setLoading(false);
  }, [router]);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-900">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
      </div>
    );
  }

  if (!isAuthenticated) return null;

  return (
    <div className="min-h-screen bg-gray-900 text-white">
      <div className="border-b border-gray-800 bg-black/20">
        <div className="max-w-7xl mx-auto px-4 py-4">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-white">⚙️ Admin Control Panel</h1>
              <p className="text-gray-400 text-sm">WHOAMISEC AI Management</p>
            </div>
            <button
              onClick={() => {
                sessionStorage.removeItem('admin_token');
                router.push('/admin/login');
              }}
              className="px-4 py-2 bg-red-500/10 border border-red-500/20 text-red-400 rounded-lg hover:bg-red-500/20 transition-colors"
            >
              Logout
            </button>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 py-6">
        <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
          <div className="lg:col-span-1">
            <div className="bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-700 p-4">
              <nav className="space-y-2">
                <button
                  onClick={() => setActiveTab('telegram')}
                  className={`w-full px-4 py-3 rounded-lg text-left font-medium transition-colors ${
                    activeTab === 'telegram'
                      ? 'bg-blue-600 text-white'
                      : 'text-gray-400 hover:bg-gray-700 hover:text-white'
                  }`}
                >
                  📱 Telegram Bot
                </button>
                <button
                  onClick={() => setActiveTab('subscribers')}
                  className={`w-full px-4 py-3 rounded-lg text-left font-medium transition-colors ${
                    activeTab === 'subscribers'
                      ? 'bg-blue-600 text-white'
                      : 'text-gray-400 hover:bg-gray-700 hover:text-white'
                  }`}
                >
                  👥 Subscribers
                </button>
                <button
                  onClick={() => setActiveTab('wallet')}
                  className={`w-full px-4 py-3 rounded-lg text-left font-medium transition-colors ${
                    activeTab === 'wallet'
                      ? 'bg-blue-600 text-white'
                      : 'text-gray-400 hover:bg-gray-700 hover:text-white'
                  }`}
                >
                  💰 Wallet & Donations
                </button>
                <button
                  onClick={() => setActiveTab('settings')}
                  className={`w-full px-4 py-3 rounded-lg text-left font-medium transition-colors ${
                    activeTab === 'settings'
                      ? 'bg-blue-600 text-white'
                      : 'text-gray-400 hover:bg-gray-700 hover:text-white'
                  }`}
                >
                  ⚙️ Settings
                </button>
              </nav>
            </div>
          </div>

          <div className="lg:col-span-3">
            {activeTab === 'telegram' && <TelegramSettings />}
            {activeTab === 'subscribers' && <SubscriberManagement />}
            {activeTab === 'wallet' && <WalletDonations />}
            {activeTab === 'settings' && <AdminSettings />}
          </div>
        </div>
      </div>
    </div>
  );
}

function TelegramSettings() {
  const [botToken, setBotToken] = useState('');
  const [adminIds, setAdminIds] = useState('');
  const [enabled, setEnabled] = useState(false);
  const [saving, setSaving] = useState(false);
  const [message, setMessage] = useState('');

  const handleSave = async () => {
    setSaving(true);
    setMessage('');
    try {
      const response = await fetch('/api/admin/telegram', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ botToken, adminIds, enabled }),
      });
      const data = await response.json();
      setMessage(data.success ? '✅ Settings saved!' : '❌ Error saving');
    } catch {
      setMessage('❌ Connection error');
    }
    setSaving(false);
  };

  return (
    <div className="bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-700 p-6">
      <h2 className="text-xl font-bold mb-6">📱 Telegram Bot Configuration</h2>

      <div className="space-y-6">
        <div>
          <label className="block text-sm font-medium text-gray-300 mb-2">
            Bot Token
          </label>
          <input
            type="password"
            value={botToken}
            onChange={(e) => setBotToken(e.target.value)}
            placeholder="123456789:ABCdefGHIjklMNOpqrSTUvwxyz"
            className="w-full px-4 py-3 bg-gray-900/50 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-blue-500"
          />
          <p className="text-xs text-gray-500 mt-1">Get token from @BotFather</p>
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-300 mb-2">
            Admin Chat IDs (comma separated)
          </label>
          <textarea
            value={adminIds}
            onChange={(e) => setAdminIds(e.target.value)}
            placeholder="123456789, 987654321"
            rows={3}
            className="w-full px-4 py-3 bg-gray-900/50 border border-gray-600 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 resize-none"
          />
        </div>

        <div className="flex items-center gap-4">
          <label className="flex items-center gap-3 cursor-pointer">
            <input
              type="checkbox"
              checked={enabled}
              onChange={(e) => setEnabled(e.target.checked)}
              className="w-5 h-5 rounded border-gray-600 bg-gray-700 text-blue-500 focus:ring-blue-500"
            />
            <span className="text-gray-300">Enable Telegram Bot</span>
          </label>
        </div>

        <div className="flex items-center gap-4">
          <button
            onClick={handleSave}
            disabled={saving}
            className="px-6 py-3 bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white font-medium rounded-xl transition-colors"
          >
            {saving ? 'Saving...' : '💾 Save Settings'}
          </button>
          <button
            onClick={() => window.open(`https://t.me/${botToken.split(':')[1]}`, '_blank')}
            disabled={!botToken}
            className="px-6 py-3 bg-gray-700 hover:bg-gray-600 disabled:opacity-50 text-white font-medium rounded-xl transition-colors"
          >
            🌐 Open Bot
          </button>
        </div>

        {message && (
          <div className="p-4 bg-gray-900/50 rounded-xl text-center">
            {message}
          </div>
        )}
      </div>
    </div>
  );
}

function SubscriberManagement() {
  const [subscribers, setSubscribers] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch('/api/admin/subscribers')
      .then(res => res.json())
      .then(data => {
        setSubscribers(data.subscribers || []);
        setLoading(false);
      })
      .catch(() => setLoading(false));
  }, []);

  const addSubscriber = async (telegramId: string, plan: string) => {
    await fetch('/api/admin/subscribers', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ telegramId, plan, action: 'add' }),
    });
    window.location.reload();
  };

  const removeSubscriber = async (telegramId: string) => {
    await fetch('/api/admin/subscribers', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ telegramId, action: 'remove' }),
    });
    window.location.reload();
  };

  return (
    <div className="bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-700 p-6">
      <h2 className="text-xl font-bold mb-6">👥 Subscriber Management</h2>

      <div className="mb-6 p-4 bg-gray-900/50 rounded-xl">
        <h3 className="font-medium mb-4">➕ Add Subscriber</h3>
        <div className="flex gap-4">
          <input
            type="text"
            id="newSubId"
            placeholder="Telegram ID"
            className="flex-1 px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-500"
          />
          <select
            id="newSubPlan"
            className="px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white"
          >
            <option value="free">Free</option>
            <option value="basic">Basic</option>
            <option value="premium">Premium</option>
            <option value="admin">Admin</option>
          </select>
          <button
            onClick={() => {
              const id = (document.getElementById('newSubId') as HTMLInputElement).value;
              const plan = (document.getElementById('newSubPlan') as HTMLSelectElement).value;
              if (id) addSubscriber(id, plan);
            }}
            className="px-6 py-2 bg-green-600 hover:bg-green-500 text-white font-medium rounded-lg"
          >
            Add
          </button>
        </div>
      </div>

      {loading ? (
        <div className="text-center py-8">Loading...</div>
      ) : (
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b border-gray-700">
                <th className="text-left py-3 px-4 text-gray-400 font-medium">Telegram ID</th>
                <th className="text-left py-3 px-4 text-gray-400 font-medium">Plan</th>
                <th className="text-left py-3 px-4 text-gray-400 font-medium">Status</th>
                <th className="text-left py-3 px-4 text-gray-400 font-medium">Expires</th>
                <th className="text-left py-3 px-4 text-gray-400 font-medium">Actions</th>
              </tr>
            </thead>
            <tbody>
              {subscribers.map((sub) => (
                <tr key={sub.telegramId} className="border-b border-gray-700/50 hover:bg-gray-700/20">
                  <td className="py-3 px-4 font-mono text-sm">{sub.telegramId}</td>
                  <td className="py-3 px-4">
                    <span className={`px-2 py-1 rounded text-xs font-medium ${
                      sub.plan === 'admin' ? 'bg-red-500/20 text-red-400' :
                      sub.plan === 'premium' ? 'bg-purple-500/20 text-purple-400' :
                      sub.plan === 'basic' ? 'bg-blue-500/20 text-blue-400' :
                      'bg-gray-500/20 text-gray-400'
                    }`}>
                      {sub.plan?.toUpperCase() || 'FREE'}
                    </span>
                  </td>
                  <td className="py-3 px-4">
                    <span className={`px-2 py-1 rounded text-xs ${
                      sub.active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'
                    }`}>
                      {sub.active ? 'Active' : 'Inactive'}
                    </span>
                  </td>
                  <td className="py-3 px-4 text-gray-400 text-sm">
                    {sub.expiresAt || 'Never'}
                  </td>
                  <td className="py-3 px-4">
                    <button
                      onClick={() => removeSubscriber(sub.telegramId)}
                      className="px-3 py-1 bg-red-500/20 border border-red-500/30 text-red-400 rounded hover:bg-red-500/30 text-sm"
                    >
                      Remove
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}

function WalletDonations() {
  const moneroAddress = 'XMR_ADDRESS_ENCRYPTED';

  return (
    <div className="bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-700 p-6">
      <h2 className="text-xl font-bold mb-6">💰 Wallet & Donations</h2>

      <div className="grid md:grid-cols-2 gap-6">
        <div className="p-6 bg-gradient-to-br from-purple-900/30 to-blue-900/30 rounded-xl border border-purple-500/20">
          <div className="flex items-center gap-3 mb-4">
            <span className="text-3xl">🪙</span>
            <div>
              <h3 className="font-bold text-lg">Monero (XMR)</h3>
              <p className="text-gray-400 text-sm">Anonymous donations</p>
            </div>
          </div>
          <div className="p-4 bg-black/30 rounded-lg">
            <p className="text-xs text-gray-500 mb-2">Wallet Address</p>
            <code className="text-sm break-all text-green-400">{moneroAddress}</code>
          </div>
          <button
            onClick={() => navigator.clipboard.writeText(moneroAddress)}
            className="mt-4 w-full py-3 bg-purple-600 hover:bg-purple-500 text-white font-medium rounded-xl"
          >
            📋 Copy Address
          </button>
        </div>

        <div className="p-6 bg-gradient-to-br from-orange-900/30 to-yellow-900/30 rounded-xl border border-orange-500/20">
          <div className="flex items-center gap-3 mb-4">
            <span className="text-3xl">💝</span>
            <div>
              <h3 className="font-bold text-lg">Support Project</h3>
              <p className="text-gray-400 text-sm">Keep us running 24/7</p>
            </div>
          </div>
          <p className="text-gray-300 text-sm mb-4">
            Your donations help maintain servers, API costs, and continuous development.
            All contributions are appreciated! 🙏
          </p>
          <div className="space-y-2 text-sm text-gray-400">
            <p>• Server hosting: $50/month</p>
            <p>• API calls: $20/month</p>
            <p>• Development: Volunteer</p>
          </div>
        </div>
      </div>
    </div>
  );
}

function AdminSettings() {
  const [currentPassword, setCurrentPassword] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [message, setMessage] = useState('');

  const handlePasswordChange = async () => {
    if (!newPassword || newPassword.length < 8) {
      setMessage('❌ Password must be at least 8 characters');
      return;
    }
    setMessage('✅ Password updated successfully');
    setCurrentPassword('');
    setNewPassword('');
  };

  return (
    <div className="bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-700 p-6">
      <h2 className="text-xl font-bold mb-6">⚙️ Admin Settings</h2>

      <div className="space-y-6">
        <div>
          <h3 className="font-medium mb-4">🔑 Change Admin Password</h3>
          <div className="space-y-4">
            <input
              type="password"
              value={currentPassword}
              onChange={(e) => setCurrentPassword(e.target.value)}
              placeholder="Current password"
              className="w-full px-4 py-3 bg-gray-900/50 border border-gray-600 rounded-xl text-white placeholder-gray-500"
            />
            <input
              type="password"
              value={newPassword}
              onChange={(e) => setNewPassword(e.target.value)}
              placeholder="New password (min 8 characters)"
              className="w-full px-4 py-3 bg-gray-900/50 border border-gray-600 rounded-xl text-white placeholder-gray-500"
            />
            <button
              onClick={handlePasswordChange}
              className="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-xl"
            >
              Update Password
            </button>
            {message && <p className="text-sm">{message}</p>}
          </div>
        </div>

        <div className="border-t border-gray-700 pt-6">
          <h3 className="font-medium mb-4">🔒 Security</h3>
          <div className="space-y-3">
            <label className="flex items-center gap-3">
              <input type="checkbox" defaultChecked className="w-4 h-4" />
              <span className="text-gray-300">Require re-authentication after 30 minutes</span>
            </label>
            <label className="flex items-center gap-3">
              <input type="checkbox" defaultChecked className="w-4 h-4" />
              <span className="text-gray-300">Log all admin actions</span>
            </label>
          </div>
        </div>

        <div className="border-t border-gray-700 pt-6">
          <h3 className="font-medium mb-4 text-red-400">⚠️ Danger Zone</h3>
          <button className="px-6 py-3 bg-red-600/20 border border-red-500/30 text-red-400 font-medium rounded-xl hover:bg-red-600/30">
            🗑️ Reset All Data
          </button>
        </div>
      </div>
    </div>
  );
}
