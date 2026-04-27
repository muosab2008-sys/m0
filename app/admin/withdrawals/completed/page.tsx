"use client";

export const dynamic = "force-dynamic";

import { useEffect, useState } from "react";
import {
  collection,
  query,
  orderBy,
  onSnapshot,
  where,
} from "firebase/firestore";
import { db } from "@/lib/firebase";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Input } from "@/components/ui/input";
import {
  DollarSign,
  CheckCircle,
  XCircle,
  Search,
  RotateCcw,
} from "lucide-react";

interface Withdrawal {
  id: string;
  userId: string;
  username: string;
  email: string;
  amountUSD: number;
  pointsDeducted: number;
  method: string;
  paymentDetails: string;
  status: "pending" | "completed" | "rejected" | "refunded";
  createdAt: Date;
  processedAt?: Date;
  ipAddress?: string;
}

export default function CompletedWithdrawalsPage() {
  const [withdrawals, setWithdrawals] = useState<Withdrawal[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");

  useEffect(() => {
    const q = query(
      collection(db, "withdrawals"),
      where("status", "in", ["completed", "rejected", "refunded"]),
      orderBy("createdAt", "desc")
    );

    const unsubscribe = onSnapshot(
      q,
      (snapshot) => {
        const data = snapshot.docs.map((doc) => {
          const d = doc.data();
          return {
            id: doc.id,
            userId: d.userId,
            username: d.username,
            email: d.email,
            amountUSD: d.amountUSD || d.amount || 0,
            pointsDeducted: d.pointsDeducted || d.coins || 0,
            method: d.method || d.method_name,
            paymentDetails: d.paymentDetails || d.payment_info,
            status: d.status,
            createdAt: d.createdAt?.toDate() || new Date(),
            processedAt: d.processedAt?.toDate(),
            ipAddress: d.ipAddress || d.ip_address,
          };
        }) as Withdrawal[];
        setWithdrawals(data);
        setLoading(false);
      },
      () => setLoading(false)
    );

    return () => unsubscribe();
  }, []);

  const completedWithdrawals = withdrawals.filter((w) => w.status === "completed");
  const rejectedWithdrawals = withdrawals.filter((w) => w.status === "rejected" || w.status === "refunded");
  const totalPaidUSD = completedWithdrawals.reduce((acc, w) => acc + (w.amountUSD || 0), 0);

  const filteredWithdrawals = withdrawals.filter(
    (w) =>
      w.username?.toLowerCase().includes(search.toLowerCase()) ||
      w.paymentDetails?.toLowerCase().includes(search.toLowerCase()) ||
      w.email?.toLowerCase().includes(search.toLowerCase())
  );

  const getStatusBadge = (status: string, refunded?: boolean) => {
    switch (status) {
      case "completed":
        return (
          <Badge className="bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 rounded-xl">
            <CheckCircle className="mr-1 h-3 w-3" />
            Paid
          </Badge>
        );
      case "refunded":
        return (
          <Badge className="bg-orange-500/10 text-orange-500 border border-orange-500/20 rounded-xl">
            <RotateCcw className="mr-1 h-3 w-3" />
            Refunded
          </Badge>
        );
      case "rejected":
        return (
          <Badge className="bg-red-500/10 text-red-500 border border-red-500/20 rounded-xl">
            <XCircle className="mr-1 h-3 w-3" />
            Rejected
          </Badge>
        );
      default:
        return null;
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl font-bold flex items-center gap-2 text-white">
          <DollarSign className="h-6 w-6 text-[#3B82F6]" />
          Completed Withdrawals
        </h1>
        <p className="text-white/50">
          View processed withdrawal history
        </p>
      </div>

      {/* Stats */}
      <div className="grid gap-4 sm:grid-cols-3">
        <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
          <CardContent className="p-5 text-center">
            <p className="text-3xl font-black text-emerald-500">{completedWithdrawals.length}</p>
            <p className="text-sm text-white/50">Paid</p>
          </CardContent>
        </Card>
        <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
          <CardContent className="p-5 text-center">
            <p className="text-3xl font-black text-[#3B82F6]">${totalPaidUSD.toFixed(2)}</p>
            <p className="text-sm text-white/50">Total Paid</p>
          </CardContent>
        </Card>
        <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
          <CardContent className="p-5 text-center">
            <p className="text-3xl font-black text-red-500">{rejectedWithdrawals.length}</p>
            <p className="text-sm text-white/50">Rejected/Refunded</p>
          </CardContent>
        </Card>
      </div>

      {/* Search */}
      <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
        <CardContent className="p-4">
          <div className="relative">
            <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white/40" />
            <Input
              placeholder="Search by username, email, or payment info..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="pl-10"
            />
          </div>
        </CardContent>
      </Card>

      {/* Withdrawals List */}
      <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
        <CardHeader>
          <CardTitle className="text-white">Withdrawal History</CardTitle>
          <CardDescription className="text-white/40">
            {filteredWithdrawals.length} total processed withdrawals
          </CardDescription>
        </CardHeader>
        <CardContent>
          {loading ? (
            <div className="space-y-3">
              {Array.from({ length: 5 }).map((_, i) => (
                <div
                  key={i}
                  className="flex items-center justify-between rounded-2xl bg-white/5 p-5 animate-pulse"
                >
                  <div className="h-5 w-40 bg-white/10 rounded-lg" />
                  <div className="h-5 w-20 bg-white/10 rounded-lg" />
                </div>
              ))}
            </div>
          ) : filteredWithdrawals.length === 0 ? (
            <p className="py-8 text-center text-white/40">
              No withdrawal history
            </p>
          ) : (
            <div className="space-y-3">
              {filteredWithdrawals.map((withdrawal) => (
                <div
                  key={withdrawal.id}
                  className={`rounded-2xl border p-5 ${
                    withdrawal.status === "completed"
                      ? "border-emerald-500/20 bg-emerald-500/5"
                      : "border-white/5 bg-white/[0.02]"
                  }`}
                >
                  <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div className="space-y-1">
                      <div className="flex items-center gap-2 flex-wrap">
                        <span className="font-black text-xl text-white">
                          ${(withdrawal.amountUSD || 0).toFixed(2)}
                        </span>
                        {getStatusBadge(withdrawal.status)}
                      </div>
                      <p className="text-sm font-medium text-white">{withdrawal.username}</p>
                      <p className="text-xs text-white/40">
                        {withdrawal.method}: {withdrawal.paymentDetails}
                      </p>
                      <p className="text-xs text-white/40">
                        Created: {withdrawal.createdAt.toLocaleString()}
                      </p>
                      {withdrawal.processedAt && (
                        <p className="text-xs text-white/40">
                          Processed: {withdrawal.processedAt.toLocaleString()}
                        </p>
                      )}
                      <p className="text-xs text-white/40">
                        Points: {withdrawal.pointsDeducted?.toLocaleString() || 0} | IP: {withdrawal.ipAddress || "N/A"}
                      </p>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
