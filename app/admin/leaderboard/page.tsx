"use client";

export const dynamic = "force-dynamic";

import { useEffect, useState } from "react";
import {
  collection,
  query,
  orderBy,
  limit,
  onSnapshot,
  doc,
  setDoc,
  getDoc,
} from "firebase/firestore";
import { db } from "@/lib/firebase";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { toast } from "sonner";
import {
  Trophy,
  Medal,
  Save,
  Loader2,
  Crown,
  Award,
} from "lucide-react";

interface LeaderboardUser {
  uid: string;
  username: string;
  points: number;
  totalEarned: number;
  level: number;
}

interface LeaderboardPrizes {
  first: string;
  second: string;
  third: string;
  fourth: string;
  fifth: string;
}

export default function AdminLeaderboardPage() {
  const [topUsers, setTopUsers] = useState<LeaderboardUser[]>([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [prizes, setPrizes] = useState<LeaderboardPrizes>({
    first: "50",
    second: "30",
    third: "15",
    fourth: "5",
    fifth: "0",
  });

  useEffect(() => {
    // Load leaderboard prizes
    const loadPrizes = async () => {
      const prizesDoc = await getDoc(doc(db, "settings", "leaderboard"));
      if (prizesDoc.exists()) {
        setPrizes(prizesDoc.data() as LeaderboardPrizes);
      }
    };
    loadPrizes();

    // Load top users
    const q = query(
      collection(db, "users"),
      orderBy("totalEarned", "desc"),
      limit(10)
    );

    const unsubscribe = onSnapshot(
      q,
      (snapshot) => {
        const data = snapshot.docs.map((doc) => {
          const d = doc.data();
          return {
            uid: doc.id,
            username: d.username || "Unknown",
            points: d.points || 0,
            totalEarned: d.totalEarned || 0,
            level: d.level || 1,
          };
        }) as LeaderboardUser[];
        setTopUsers(data);
        setLoading(false);
      },
      () => setLoading(false)
    );

    return () => unsubscribe();
  }, []);

  const savePrizes = async () => {
    setSaving(true);
    try {
      await setDoc(doc(db, "settings", "leaderboard"), prizes);
      toast.success("Prizes saved successfully");
    } catch (error) {
      toast.error("Failed to save prizes");
    } finally {
      setSaving(false);
    }
  };

  const getRankIcon = (rank: number) => {
    switch (rank) {
      case 1:
        return <Crown className="h-6 w-6 text-yellow-400" />;
      case 2:
        return <Medal className="h-6 w-6 text-gray-400" />;
      case 3:
        return <Medal className="h-6 w-6 text-amber-600" />;
      default:
        return <Award className="h-5 w-5 text-white/30" />;
    }
  };

  const getRankBg = (rank: number) => {
    switch (rank) {
      case 1:
        return "bg-gradient-to-r from-yellow-500/20 to-amber-500/20 border-yellow-500/30";
      case 2:
        return "bg-gradient-to-r from-gray-500/20 to-slate-500/20 border-gray-500/30";
      case 3:
        return "bg-gradient-to-r from-amber-600/20 to-orange-600/20 border-amber-600/30";
      default:
        return "bg-white/[0.02] border-white/5";
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl font-bold flex items-center gap-2 text-white">
          <Trophy className="h-6 w-6 text-[#3B82F6]" />
          Leaderboard Management
        </h1>
        <p className="text-white/50">
          Configure leaderboard prizes and view top earners
        </p>
      </div>

      {/* Prize Configuration */}
      <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
        <CardHeader>
          <CardTitle className="flex items-center gap-2 text-white">
            <Trophy className="h-5 w-5 text-amber-500" />
            Weekly Prize Pool
          </CardTitle>
          <CardDescription className="text-white/40">
            Configure the prizes for top leaderboard positions (in USD)
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid gap-4 sm:grid-cols-5">
            <div className="space-y-2">
              <label className="text-sm font-medium text-yellow-400 flex items-center gap-1">
                <Crown className="h-4 w-4" /> 1st Place
              </label>
              <div className="relative">
                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-white/40">$</span>
                <Input
                  type="number"
                  value={prizes.first}
                  onChange={(e) => setPrizes({ ...prizes, first: e.target.value })}
                  className="pl-7"
                />
              </div>
            </div>
            <div className="space-y-2">
              <label className="text-sm font-medium text-gray-400 flex items-center gap-1">
                <Medal className="h-4 w-4" /> 2nd Place
              </label>
              <div className="relative">
                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-white/40">$</span>
                <Input
                  type="number"
                  value={prizes.second}
                  onChange={(e) => setPrizes({ ...prizes, second: e.target.value })}
                  className="pl-7"
                />
              </div>
            </div>
            <div className="space-y-2">
              <label className="text-sm font-medium text-amber-600 flex items-center gap-1">
                <Medal className="h-4 w-4" /> 3rd Place
              </label>
              <div className="relative">
                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-white/40">$</span>
                <Input
                  type="number"
                  value={prizes.third}
                  onChange={(e) => setPrizes({ ...prizes, third: e.target.value })}
                  className="pl-7"
                />
              </div>
            </div>
            <div className="space-y-2">
              <label className="text-sm font-medium text-white/50">4th Place</label>
              <div className="relative">
                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-white/40">$</span>
                <Input
                  type="number"
                  value={prizes.fourth}
                  onChange={(e) => setPrizes({ ...prizes, fourth: e.target.value })}
                  className="pl-7"
                />
              </div>
            </div>
            <div className="space-y-2">
              <label className="text-sm font-medium text-white/50">5th Place</label>
              <div className="relative">
                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-white/40">$</span>
                <Input
                  type="number"
                  value={prizes.fifth}
                  onChange={(e) => setPrizes({ ...prizes, fifth: e.target.value })}
                  className="pl-7"
                />
              </div>
            </div>
          </div>
          <div className="flex justify-end">
            <Button
              onClick={savePrizes}
              disabled={saving}
              className="bg-gradient-to-r from-[#3B82F6] to-[#8B5CF6] text-white rounded-xl"
            >
              {saving ? (
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              ) : (
                <Save className="mr-2 h-4 w-4" />
              )}
              Save Prizes
            </Button>
          </div>
        </CardContent>
      </Card>

      {/* Current Leaderboard */}
      <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
        <CardHeader>
          <CardTitle className="text-white">Current Leaderboard</CardTitle>
          <CardDescription className="text-white/40">
            Top 10 earners this period
          </CardDescription>
        </CardHeader>
        <CardContent>
          {loading ? (
            <div className="space-y-3">
              {Array.from({ length: 5 }).map((_, i) => (
                <div
                  key={i}
                  className="flex items-center justify-between rounded-2xl bg-white/5 p-4 animate-pulse"
                >
                  <div className="h-6 w-40 bg-white/10 rounded-lg" />
                  <div className="h-6 w-20 bg-white/10 rounded-lg" />
                </div>
              ))}
            </div>
          ) : topUsers.length === 0 ? (
            <p className="py-8 text-center text-white/40">
              No users yet
            </p>
          ) : (
            <div className="space-y-3">
              {topUsers.map((user, index) => {
                const rank = index + 1;
                const prizeValues = [prizes.first, prizes.second, prizes.third, prizes.fourth, prizes.fifth];
                const prize = prizeValues[rank - 1] || "0";
                
                return (
                  <div
                    key={user.uid}
                    className={`rounded-2xl border p-4 ${getRankBg(rank)}`}
                  >
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-4">
                        <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-white/5">
                          {getRankIcon(rank)}
                        </div>
                        <div>
                          <div className="flex items-center gap-2">
                            <span className="font-bold text-white">#{rank}</span>
                            <span className="font-medium text-white">{user.username}</span>
                          </div>
                          <p className="text-xs text-white/40">
                            Level {user.level} | {user.points.toLocaleString()} points
                          </p>
                        </div>
                      </div>
                      <div className="text-right">
                        <p className="text-lg font-black text-emerald-500">
                          {user.totalEarned.toLocaleString()}
                        </p>
                        <p className="text-xs text-white/40">Total Earned</p>
                        {parseFloat(prize) > 0 && (
                          <p className="text-sm font-bold text-amber-400">
                            Prize: ${prize}
                          </p>
                        )}
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
