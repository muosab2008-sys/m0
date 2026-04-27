"use client";

import { useState, useEffect } from "react";
import { doc, getDoc, setDoc } from "firebase/firestore";
import { db } from "@/lib/firebase";
import { useAuth } from "@/contexts/auth-context";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Switch } from "@/components/ui/switch";
import { toast } from "sonner";
import { Loader2, Save, Settings, DollarSign, Gift, Users, ArrowLeft } from "lucide-react";
import Link from "next/link";

interface PlatformSettings {
  pointsPerUsd: number;
  minWithdrawal: number;
  referralBonus: number;
  referralCommission: number;
  dailyBonusEnabled: boolean;
  dailyBonusPoints: number;
  levelUpBonus: number;
  maintenanceMode: boolean;
  registrationEnabled: boolean;
  withdrawalsEnabled: boolean;
}

const defaultSettings: PlatformSettings = {
  pointsPerUsd: 1000,
  minWithdrawal: 5000,
  referralBonus: 100,
  referralCommission: 10,
  dailyBonusEnabled: true,
  dailyBonusPoints: 10,
  levelUpBonus: 50,
  maintenanceMode: false,
  registrationEnabled: true,
  withdrawalsEnabled: true,
};

export default function AdminSettingsPage() {
  const { userData } = useAuth();
  const [settings, setSettings] = useState<PlatformSettings>(defaultSettings);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    loadSettings();
  }, []);

  const loadSettings = async () => {
    try {
      const settingsDoc = await getDoc(doc(db, "settings", "platform"));
      if (settingsDoc.exists()) {
        setSettings({ ...defaultSettings, ...settingsDoc.data() as PlatformSettings });
      }
    } catch (error) {
      console.error("Error loading settings:", error);
    } finally {
      setLoading(false);
    }
  };

  const saveSettings = async () => {
    setSaving(true);
    try {
      await setDoc(doc(db, "settings", "platform"), settings);
      toast.success("Settings saved successfully");
    } catch (error) {
      console.error("Error saving settings:", error);
      toast.error("Failed to save settings");
    } finally {
      setSaving(false);
    }
  };

  const updateSetting = <K extends keyof PlatformSettings>(key: K, value: PlatformSettings[K]) => {
    setSettings((prev) => ({ ...prev, [key]: value }));
  };

  if (!userData?.isAdmin) {
    return null;
  }

  if (loading) {
    return (
      <div className="flex min-h-screen items-center justify-center bg-background">
        <Loader2 className="h-8 w-8 animate-spin text-[#3B82F6]" />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background">
      <div className="mx-auto max-w-7xl space-y-6 p-4 sm:p-6">
        {/* Header */}
        <div className="flex items-center gap-4">
          <Link href="/admin">
            <Button variant="ghost" size="icon" className="rounded-xl hover:bg-white/5">
              <ArrowLeft className="h-5 w-5 text-white" />
            </Button>
          </Link>
          <div>
            <h1 className="text-2xl font-bold text-white">Platform Settings</h1>
            <p className="text-white/50">Configure your MrCash platform</p>
          </div>
        </div>

        <div className="grid gap-6 md:grid-cols-2">
          {/* Points & Withdrawal Settings */}
          <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
            <CardHeader>
              <CardTitle className="flex items-center gap-2 text-white">
                <DollarSign className="h-5 w-5 text-[#3B82F6]" />
                Points & Withdrawals
              </CardTitle>
              <CardDescription className="text-white/40">Configure points conversion and withdrawal settings</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <label className="text-sm font-medium text-white">Points per $1 USD</label>
                <Input
                  type="number"
                  value={settings.pointsPerUsd}
                  onChange={(e) => updateSetting("pointsPerUsd", parseInt(e.target.value) || 0)}
                  className="bg-white/5 border-white/10 text-white"
                />
                <p className="text-xs text-white/40">How many points users earn per $1 from offerwalls</p>
              </div>
              <div className="space-y-2">
                <label className="text-sm font-medium text-white">Minimum Withdrawal (points)</label>
                <Input
                  type="number"
                  value={settings.minWithdrawal}
                  onChange={(e) => updateSetting("minWithdrawal", parseInt(e.target.value) || 0)}
                  className="bg-white/5 border-white/10 text-white"
                />
                <p className="text-xs text-white/40">Minimum points required to withdraw</p>
              </div>
              <div className="flex items-center justify-between">
                <div>
                  <label className="text-sm font-medium text-white">Enable Withdrawals</label>
                  <p className="text-xs text-white/40">Allow users to withdraw</p>
                </div>
                <Switch
                  checked={settings.withdrawalsEnabled}
                  onCheckedChange={(checked) => updateSetting("withdrawalsEnabled", checked)}
                />
              </div>
            </CardContent>
          </Card>

          {/* Referral Settings */}
          <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
            <CardHeader>
              <CardTitle className="flex items-center gap-2 text-white">
                <Users className="h-5 w-5 text-[#8B5CF6]" />
                Referral Program
              </CardTitle>
              <CardDescription className="text-white/40">Configure referral bonuses and commissions</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <label className="text-sm font-medium text-white">Signup Bonus (points)</label>
                <Input
                  type="number"
                  value={settings.referralBonus}
                  onChange={(e) => updateSetting("referralBonus", parseInt(e.target.value) || 0)}
                  className="bg-white/5 border-white/10 text-white"
                />
                <p className="text-xs text-white/40">Points given to referrer when someone signs up</p>
              </div>
              <div className="space-y-2">
                <label className="text-sm font-medium text-white">Commission Rate (%)</label>
                <Input
                  type="number"
                  value={settings.referralCommission}
                  onChange={(e) => updateSetting("referralCommission", parseInt(e.target.value) || 0)}
                  min={0}
                  max={100}
                  className="bg-white/5 border-white/10 text-white"
                />
                <p className="text-xs text-white/40">Percentage of referral earnings given to referrer</p>
              </div>
            </CardContent>
          </Card>

          {/* Bonus Settings */}
          <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
            <CardHeader>
              <CardTitle className="flex items-center gap-2 text-white">
                <Gift className="h-5 w-5 text-[#3B82F6]" />
                Bonuses
              </CardTitle>
              <CardDescription className="text-white/40">Configure daily and level bonuses</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex items-center justify-between">
                <div>
                  <label className="text-sm font-medium text-white">Daily Bonus</label>
                  <p className="text-xs text-white/40">Enable daily login bonus</p>
                </div>
                <Switch
                  checked={settings.dailyBonusEnabled}
                  onCheckedChange={(checked) => updateSetting("dailyBonusEnabled", checked)}
                />
              </div>
              <div className="space-y-2">
                <label className="text-sm font-medium text-white">Daily Bonus Points</label>
                <Input
                  type="number"
                  value={settings.dailyBonusPoints}
                  onChange={(e) => updateSetting("dailyBonusPoints", parseInt(e.target.value) || 0)}
                  disabled={!settings.dailyBonusEnabled}
                  className="bg-white/5 border-white/10 text-white disabled:opacity-50"
                />
              </div>
              <div className="space-y-2">
                <label className="text-sm font-medium text-white">Level Up Bonus (points)</label>
                <Input
                  type="number"
                  value={settings.levelUpBonus}
                  onChange={(e) => updateSetting("levelUpBonus", parseInt(e.target.value) || 0)}
                  className="bg-white/5 border-white/10 text-white"
                />
                <p className="text-xs text-white/40">Bonus points when user levels up</p>
              </div>
            </CardContent>
          </Card>

          {/* System Settings */}
          <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
            <CardHeader>
              <CardTitle className="flex items-center gap-2 text-white">
                <Settings className="h-5 w-5 text-[#8B5CF6]" />
                System
              </CardTitle>
              <CardDescription className="text-white/40">System-wide settings</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex items-center justify-between">
                <div>
                  <label className="text-sm font-medium text-white">Maintenance Mode</label>
                  <p className="text-xs text-white/40">Disable site access for users</p>
                </div>
                <Switch
                  checked={settings.maintenanceMode}
                  onCheckedChange={(checked) => updateSetting("maintenanceMode", checked)}
                />
              </div>
              <div className="flex items-center justify-between">
                <div>
                  <label className="text-sm font-medium text-white">Enable Registration</label>
                  <p className="text-xs text-white/40">Allow new user signups</p>
                </div>
                <Switch
                  checked={settings.registrationEnabled}
                  onCheckedChange={(checked) => updateSetting("registrationEnabled", checked)}
                />
              </div>
            </CardContent>
          </Card>
        </div>

        <div className="flex justify-end">
          <Button
            onClick={saveSettings}
            disabled={saving}
            className="bg-gradient-to-r from-[#3B82F6] to-[#8B5CF6] text-white rounded-xl"
          >
            {saving ? (
              <>
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                Saving...
              </>
            ) : (
              <>
                <Save className="mr-2 h-4 w-4" />
                Save Settings
              </>
            )}
          </Button>
        </div>

        {/* Postback URL Information */}
        <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
          <CardHeader>
            <CardTitle className="text-white">Postback URLs</CardTitle>
            <CardDescription className="text-white/40">Use these URLs to configure your offerwalls</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="rounded-xl bg-white/5 p-4">
              <p className="mb-2 text-sm font-medium text-white">Credit Postback:</p>
              <code className="block overflow-x-auto rounded-lg bg-black/50 p-2 text-xs text-white/70">
                {typeof window !== "undefined" ? window.location.origin : ""}/api/postback?wall=WALL_NAME&user_id=USER_ID&transaction_id=TX_ID&payout=AMOUNT&offer_name=OFFER
              </code>
            </div>
            <div className="rounded-xl bg-white/5 p-4">
              <p className="mb-2 text-sm font-medium text-white">Chargeback Postback:</p>
              <code className="block overflow-x-auto rounded-lg bg-black/50 p-2 text-xs text-white/70">
                {typeof window !== "undefined" ? window.location.origin : ""}/api/postback/chargeback?wall=WALL_NAME&user_id=USER_ID&transaction_id=TX_ID
              </code>
            </div>
            <p className="text-xs text-white/40">
              Replace WALL_NAME with: lootably, offertoro, adgatemedia, cpxresearch, bitlabs, timewall, ayet, notik, torox, revu, mychips, hangmyads, mmwall
            </p>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
