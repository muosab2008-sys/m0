"use client";

export const dynamic = "force-dynamic";

import { useEffect, useState } from "react";
import {
  collection,
  query,
  orderBy,
  onSnapshot,
  doc,
  setDoc,
  updateDoc,
  deleteDoc,
  serverTimestamp,
} from "firebase/firestore";
import { db } from "@/lib/firebase";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Switch } from "@/components/ui/switch";
import { toast } from "sonner";
import {
  Ticket,
  Plus,
  Trash2,
  ArrowLeft,
  Loader2,
  Copy,
  Coins,
  Zap,
} from "lucide-react";
import Link from "next/link";

interface PromoCode {
  id: string;
  code: string;
  pointsReward: number;
  fragmentsReward: number;
  maxUses: number;
  usesCount: number;
  isActive: boolean;
  createdAt: Date;
}

export default function AdminPromoPage() {
  const [promoCodes, setPromoCodes] = useState<PromoCode[]>([]);
  const [loading, setLoading] = useState(true);
  const [creating, setCreating] = useState(false);
  
  // Form state
  const [newCode, setNewCode] = useState("");
  const [newPoints, setNewPoints] = useState("");
  const [newFragments, setNewFragments] = useState("");
  const [newMaxUses, setNewMaxUses] = useState("");

  useEffect(() => {
    const q = query(collection(db, "promoCodes"), orderBy("createdAt", "desc"));

    const unsubscribe = onSnapshot(
      q,
      (snapshot) => {
        const data = snapshot.docs.map((doc) => {
          const d = doc.data();
          return {
            id: doc.id,
            code: doc.id,
            pointsReward: d.pointsReward || 0,
            fragmentsReward: d.fragmentsReward || 0,
            maxUses: d.maxUses || 0,
            usesCount: d.usesCount || 0,
            isActive: d.isActive ?? true,
            createdAt: d.createdAt?.toDate() || new Date(),
          };
        }) as PromoCode[];
        setPromoCodes(data);
        setLoading(false);
      },
      () => setLoading(false)
    );

    return () => unsubscribe();
  }, []);

  const createPromoCode = async () => {
    if (!newCode.trim()) {
      toast.error("Please enter a code");
      return;
    }

    const points = parseInt(newPoints) || 0;
    const fragments = parseInt(newFragments) || 0;
    const maxUses = parseInt(newMaxUses) || 0;

    if (points === 0 && fragments === 0) {
      toast.error("Please enter at least points or fragments reward");
      return;
    }

    setCreating(true);
    try {
      await setDoc(doc(db, "promoCodes", newCode.toUpperCase()), {
        pointsReward: points,
        fragmentsReward: fragments,
        maxUses: maxUses,
        usesCount: 0,
        isActive: true,
        createdAt: serverTimestamp(),
      });

      toast.success("Promo code created!");
      setNewCode("");
      setNewPoints("");
      setNewFragments("");
      setNewMaxUses("");
    } catch (error: unknown) {
      const errorMessage = error instanceof Error ? error.message : "Failed to create promo code";
      toast.error(errorMessage);
    } finally {
      setCreating(false);
    }
  };

  const toggleActive = async (code: PromoCode) => {
    try {
      await updateDoc(doc(db, "promoCodes", code.id), {
        isActive: !code.isActive,
      });
      toast.success(code.isActive ? "Code deactivated" : "Code activated");
    } catch (error: unknown) {
      const errorMessage = error instanceof Error ? error.message : "Failed to update code";
      toast.error(errorMessage);
    }
  };

  const deleteCode = async (id: string) => {
    try {
      await deleteDoc(doc(db, "promoCodes", id));
      toast.success("Promo code deleted");
    } catch (error: unknown) {
      const errorMessage = error instanceof Error ? error.message : "Failed to delete code";
      toast.error(errorMessage);
    }
  };

  const copyCode = async (code: string) => {
    await navigator.clipboard.writeText(code);
    toast.success("Code copied");
  };

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
            <h1 className="text-2xl font-bold flex items-center gap-2 text-white">
              <Ticket className="h-6 w-6 text-[#3B82F6]" />
              Promo Codes
            </h1>
            <p className="text-white/50">
              Create and manage promotional codes
            </p>
          </div>
        </div>

        {/* Create New Code */}
        <Card className="border-[#3B82F6]/30 bg-[#0a0a0a] rounded-2xl">
          <CardHeader>
            <CardTitle className="flex items-center gap-2 text-white">
              <Plus className="h-5 w-5" />
              Create New Code
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
              <Input
                placeholder="CODE"
                value={newCode}
                onChange={(e) => setNewCode(e.target.value.toUpperCase())}
                className="uppercase font-mono bg-white/5 border-white/10 text-white placeholder:text-white/40"
              />
              <div className="relative">
                <Coins className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white/40" />
                <Input
                  type="number"
                  placeholder="Points"
                  value={newPoints}
                  onChange={(e) => setNewPoints(e.target.value)}
                  className="pl-10 bg-white/5 border-white/10 text-white placeholder:text-white/40"
                />
              </div>
              <div className="relative">
                <Zap className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white/40" />
                <Input
                  type="number"
                  placeholder="Fragments"
                  value={newFragments}
                  onChange={(e) => setNewFragments(e.target.value)}
                  className="pl-10 bg-white/5 border-white/10 text-white placeholder:text-white/40"
                />
              </div>
              <Input
                type="number"
                placeholder="Max Uses (0 = unlimited)"
                value={newMaxUses}
                onChange={(e) => setNewMaxUses(e.target.value)}
                className="bg-white/5 border-white/10 text-white placeholder:text-white/40"
              />
              <Button
                onClick={createPromoCode}
                disabled={creating}
                className="bg-gradient-to-r from-[#3B82F6] to-[#8B5CF6] text-white rounded-xl"
              >
                {creating ? (
                  <Loader2 className="h-4 w-4 animate-spin" />
                ) : (
                  <>
                    <Plus className="mr-2 h-4 w-4" />
                    Create
                  </>
                )}
              </Button>
            </div>
          </CardContent>
        </Card>

        {/* Codes List */}
        <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
          <CardHeader>
            <CardTitle className="text-white">All Promo Codes</CardTitle>
            <CardDescription className="text-white/40">{promoCodes.length} codes</CardDescription>
          </CardHeader>
          <CardContent>
            {loading ? (
              <div className="space-y-3">
                {Array.from({ length: 3 }).map((_, i) => (
                  <div
                    key={i}
                    className="flex items-center justify-between rounded-2xl bg-white/5 p-5 animate-pulse"
                  >
                    <div className="h-5 w-40 bg-white/10 rounded-lg" />
                    <div className="h-5 w-20 bg-white/10 rounded-lg" />
                  </div>
                ))}
              </div>
            ) : promoCodes.length === 0 ? (
              <p className="py-8 text-center text-white/40">
                No promo codes yet
              </p>
            ) : (
              <div className="space-y-3">
                {promoCodes.map((code) => (
                  <div
                    key={code.id}
                    className={`rounded-2xl border p-5 ${
                      code.isActive
                        ? "border-white/10 bg-white/[0.02]"
                        : "border-white/5 bg-white/[0.01] opacity-60"
                    }`}
                  >
                    <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                      <div className="flex items-center gap-3">
                        <div className="flex items-center gap-2">
                          <span className="font-mono font-bold text-lg text-white">{code.code}</span>
                          <Button
                            variant="ghost"
                            size="icon"
                            className="h-6 w-6 hover:bg-white/5"
                            onClick={() => copyCode(code.code)}
                          >
                            <Copy className="h-3 w-3 text-white/40" />
                          </Button>
                        </div>
                        {!code.isActive && (
                          <Badge variant="secondary" className="bg-white/10 text-white/50">Inactive</Badge>
                        )}
                      </div>

                      <div className="flex items-center gap-6">
                        <div className="flex items-center gap-4 text-sm">
                          <div className="flex items-center gap-1">
                            <Coins className="h-4 w-4 text-[#3B82F6]" />
                            <span className="text-white">{code.pointsReward}</span>
                          </div>
                          <div className="flex items-center gap-1">
                            <Zap className="h-4 w-4 text-[#8B5CF6]" />
                            <span className="text-white">{code.fragmentsReward}</span>
                          </div>
                          <span className="text-white/40">
                            {code.usesCount}/{code.maxUses || "\u221E"} uses
                          </span>
                        </div>

                        <div className="flex items-center gap-2">
                          <Switch
                            checked={code.isActive}
                            onCheckedChange={() => toggleActive(code)}
                          />
                          <Button
                            variant="ghost"
                            size="icon"
                            className="text-red-500 hover:text-red-400 hover:bg-red-500/10 rounded-xl"
                            onClick={() => deleteCode(code.id)}
                          >
                            <Trash2 className="h-4 w-4" />
                          </Button>
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
