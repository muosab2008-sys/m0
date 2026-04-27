"use client";

export const dynamic = "force-dynamic";

import { useEffect, useState } from "react";
import {
  collection,
  query,
  orderBy,
  onSnapshot,
  doc,
  updateDoc,
  writeBatch,
  where,
  serverTimestamp,
  increment,
} from "firebase/firestore";
import { db } from "@/lib/firebase";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from "@/components/ui/input";
import { toast } from "sonner";
import {
  DollarSign,
  Clock,
  CheckCircle,
  XCircle,
  Loader2,
  CheckCheck,
  RotateCcw,
  Ban,
  Search,
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
  status: "pending" | "completed" | "rejected";
  createdAt: Date;
  processedAt?: Date;
  ipAddress?: string;
}

export default function AdminWithdrawalsPage() {
  const [withdrawals, setWithdrawals] = useState<Withdrawal[]>([]);
  const [loading, setLoading] = useState(true);
  const [processing, setProcessing] = useState<string | null>(null);
  const [markingAll, setMarkingAll] = useState(false);
  const [selectedIds, setSelectedIds] = useState<Set<string>>(new Set());
  const [search, setSearch] = useState("");

  useEffect(() => {
    const q = query(
      collection(db, "withdrawals"),
      where("status", "==", "pending"),
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

  const updateWithdrawalStatus = async (
    id: string,
    status: "completed" | "rejected",
    refundPoints: boolean = false,
    userId?: string,
    pointsToRefund?: number
  ) => {
    setProcessing(id);
    try {
      const batch = writeBatch(db);

      const withdrawalRef = doc(db, "withdrawals", id);
      batch.update(withdrawalRef, {
        status,
        processedAt: serverTimestamp(),
        refunded: refundPoints,
      });

      if (status === "rejected" && refundPoints && userId && pointsToRefund) {
        const userRef = doc(db, "users", userId);
        batch.update(userRef, {
          points: increment(pointsToRefund),
        });
      }

      await batch.commit();
      
      if (status === "completed") {
        toast.success("Withdrawal approved");
      } else if (refundPoints) {
        toast.success("Withdrawal rejected - Points refunded");
      } else {
        toast.success("Withdrawal rejected - No refund");
      }
    } catch (error: unknown) {
      const errorMessage = error instanceof Error ? error.message : "Failed to update withdrawal";
      toast.error(errorMessage);
    } finally {
      setProcessing(null);
    }
  };

  const pendingWithdrawals = withdrawals.filter((w) => w.status === "pending");
  const totalPendingUSD = pendingWithdrawals.reduce((acc, w) => acc + (w.amountUSD || 0), 0);
  
  const filteredWithdrawals = pendingWithdrawals.filter(
    (w) =>
      w.username?.toLowerCase().includes(search.toLowerCase()) ||
      w.paymentDetails?.toLowerCase().includes(search.toLowerCase()) ||
      w.email?.toLowerCase().includes(search.toLowerCase())
  );

  const toggleSelection = (id: string) => {
    const newSelected = new Set(selectedIds);
    if (newSelected.has(id)) {
      newSelected.delete(id);
    } else {
      newSelected.add(id);
    }
    setSelectedIds(newSelected);
  };

  const selectAll = () => {
    if (selectedIds.size === filteredWithdrawals.length) {
      setSelectedIds(new Set());
    } else {
      setSelectedIds(new Set(filteredWithdrawals.map((w) => w.id)));
    }
  };

  const approveSelected = async () => {
    if (selectedIds.size === 0) {
      toast.error("No withdrawals selected");
      return;
    }

    setMarkingAll(true);
    try {
      const batch = writeBatch(db);

      for (const id of selectedIds) {
        const ref = doc(db, "withdrawals", id);
        batch.update(ref, {
          status: "completed",
          processedAt: serverTimestamp(),
        });
      }

      await batch.commit();
      toast.success(`Approved ${selectedIds.size} withdrawals`);
      setSelectedIds(new Set());
    } catch (error: unknown) {
      const errorMessage = error instanceof Error ? error.message : "Failed to approve selected";
      toast.error(errorMessage);
    } finally {
      setMarkingAll(false);
    }
  };

  const markAllAsPaid = async () => {
    if (filteredWithdrawals.length === 0) {
      toast.info("No pending withdrawals");
      return;
    }

    setMarkingAll(true);
    try {
      const batch = writeBatch(db);

      for (const withdrawal of filteredWithdrawals) {
        const ref = doc(db, "withdrawals", withdrawal.id);
        batch.update(ref, {
          status: "completed",
          processedAt: serverTimestamp(),
        });
      }

      await batch.commit();
      toast.success(`Marked ${filteredWithdrawals.length} withdrawals as paid`);
      setSelectedIds(new Set());
    } catch (error: unknown) {
      const errorMessage = error instanceof Error ? error.message : "Failed to mark all as paid";
      toast.error(errorMessage);
    } finally {
      setMarkingAll(false);
    }
  };

  const getStatusBadge = (status: string) => {
    switch (status) {
      case "completed":
        return (
          <Badge className="bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 rounded-xl">
            <CheckCircle className="mr-1 h-3 w-3" />
            Completed
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
        return (
          <Badge className="bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-xl">
            <Clock className="mr-1 h-3 w-3" />
            Pending
          </Badge>
        );
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between flex-wrap gap-4">
        <div>
          <h1 className="text-2xl font-bold flex items-center gap-2 text-white">
            <DollarSign className="h-6 w-6 text-[#3B82F6]" />
            Pending Withdrawals
          </h1>
          <p className="text-white/50">
            Process withdrawal requests
          </p>
        </div>
        {filteredWithdrawals.length > 0 && (
          <div className="flex items-center gap-2 flex-wrap">
            {selectedIds.size > 0 && (
              <Button
                onClick={approveSelected}
                disabled={markingAll}
                className="bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl"
              >
                {markingAll ? (
                  <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                ) : (
                  <CheckCircle className="mr-2 h-4 w-4" />
                )}
                Approve Selected ({selectedIds.size})
              </Button>
            )}
            <Button
              onClick={markAllAsPaid}
              disabled={markingAll}
              className="bg-gradient-to-r from-[#3B82F6] to-[#8B5CF6] text-white rounded-xl"
            >
              {markingAll ? (
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              ) : (
                <CheckCheck className="mr-2 h-4 w-4" />
              )}
              Approve All ({filteredWithdrawals.length})
            </Button>
          </div>
        )}
      </div>

      {/* Stats */}
      <div className="grid gap-4 sm:grid-cols-3">
        <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
          <CardContent className="p-5 text-center">
            <p className="text-3xl font-black text-amber-500">{pendingWithdrawals.length}</p>
            <p className="text-sm text-white/50">Pending</p>
          </CardContent>
        </Card>
        <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
          <CardContent className="p-5 text-center">
            <p className="text-3xl font-black text-[#8B5CF6]">${totalPendingUSD.toFixed(2)}</p>
            <p className="text-sm text-white/50">Pending Amount</p>
          </CardContent>
        </Card>
        <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
          <CardContent className="p-5 text-center">
            <p className="text-3xl font-black text-[#3B82F6]">{selectedIds.size}</p>
            <p className="text-sm text-white/50">Selected</p>
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
          <div className="flex items-center justify-between flex-wrap gap-4">
            <div>
              <CardTitle className="text-white">Pending Withdrawals</CardTitle>
              <CardDescription className="text-white/40">
                {filteredWithdrawals.length} pending requests
              </CardDescription>
            </div>
            {filteredWithdrawals.length > 0 && (
              <div className="flex items-center gap-2">
                <Checkbox
                  checked={selectedIds.size === filteredWithdrawals.length && filteredWithdrawals.length > 0}
                  onCheckedChange={selectAll}
                />
                <span className="text-sm text-white/50">Select All</span>
              </div>
            )}
          </div>
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
              No pending withdrawals
            </p>
          ) : (
            <div className="space-y-3">
              {filteredWithdrawals.map((withdrawal) => (
                <div
                  key={withdrawal.id}
                  className="rounded-2xl border border-amber-500/20 bg-amber-500/5 p-5"
                >
                  <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div className="flex items-start gap-3">
                      <Checkbox
                        checked={selectedIds.has(withdrawal.id)}
                        onCheckedChange={() => toggleSelection(withdrawal.id)}
                        className="mt-1"
                      />
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
                          {withdrawal.createdAt.toLocaleString()}
                        </p>
                        <p className="text-xs text-white/40">
                          Points: {withdrawal.pointsDeducted?.toLocaleString() || 0} | IP: {withdrawal.ipAddress || "N/A"}
                        </p>
                      </div>
                    </div>

                    <div className="flex flex-wrap gap-2">
                      <Button
                        size="sm"
                        onClick={() =>
                          updateWithdrawalStatus(withdrawal.id, "completed", false)
                        }
                        disabled={processing === withdrawal.id}
                        className="bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl"
                      >
                        {processing === withdrawal.id ? (
                          <Loader2 className="h-4 w-4 animate-spin" />
                        ) : (
                          <>
                            <CheckCircle className="mr-1 h-4 w-4" />
                            Approve
                          </>
                        )}
                      </Button>
                      
                      <Button
                        variant="outline"
                        size="sm"
                        onClick={() =>
                          updateWithdrawalStatus(
                            withdrawal.id,
                            "rejected",
                            true,
                            withdrawal.userId,
                            withdrawal.pointsDeducted
                          )
                        }
                        disabled={processing === withdrawal.id}
                        className="text-orange-500 border-orange-500/30 hover:bg-orange-500/10 rounded-xl"
                      >
                        {processing === withdrawal.id ? (
                          <Loader2 className="h-4 w-4 animate-spin" />
                        ) : (
                          <>
                            <RotateCcw className="mr-1 h-4 w-4" />
                            Reject & Refund
                          </>
                        )}
                      </Button>
                      
                      <Button
                        variant="outline"
                        size="sm"
                        onClick={() =>
                          updateWithdrawalStatus(
                            withdrawal.id,
                            "rejected",
                            false
                          )
                        }
                        disabled={processing === withdrawal.id}
                        className="text-red-500 border-red-500/30 hover:bg-red-500/10 rounded-xl"
                      >
                        {processing === withdrawal.id ? (
                          <Loader2 className="h-4 w-4 animate-spin" />
                        ) : (
                          <>
                            <Ban className="mr-1 h-4 w-4" />
                            Reject
                          </>
                        )}
                      </Button>
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
