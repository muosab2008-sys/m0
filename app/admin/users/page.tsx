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
  increment,
} from "firebase/firestore";
import { db } from "@/lib/firebase";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Switch } from "@/components/ui/switch";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { toast } from "sonner";
import {
  Users,
  Search,
  Shield,
  Ban,
  CheckCircle,
  Copy,
  ArrowLeft,
  Plus,
  Minus,
  Loader2,
} from "lucide-react";
import Link from "next/link";

interface User {
  uid: string;
  username: string;
  email: string;
  points: number;
  fragments: number;
  level: number;
  totalEarned: number;
  isAdmin: boolean;
  isBanned: boolean;
  createdAt: Date;
}

export default function AdminUsersPage() {
  const [users, setUsers] = useState<User[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");
  const [updatingUser, setUpdatingUser] = useState<string | null>(null);
  
  // Points dialog state
  const [pointsDialog, setPointsDialog] = useState<{
    open: boolean;
    type: "add" | "deduct";
    user: User | null;
  }>({ open: false, type: "add", user: null });
  const [pointsAmount, setPointsAmount] = useState("");

  useEffect(() => {
    const q = query(collection(db, "users"), orderBy("createdAt", "desc"));

    const unsubscribe = onSnapshot(
      q,
      (snapshot) => {
        const data = snapshot.docs.map((doc) => {
          const d = doc.data();
          return {
            uid: doc.id,
            username: d.username,
            email: d.email,
            points: d.points || 0,
            fragments: d.fragments || 0,
            level: d.level || 1,
            totalEarned: d.totalEarned || 0,
            isAdmin: d.isAdmin || false,
            isBanned: d.isBanned || false,
            createdAt: (d.createdAt && typeof d.createdAt.toDate === 'function') ? d.createdAt.toDate() : new Date(),
          };
        }) as User[];
        setUsers(data);
        setLoading(false);
      },
      () => setLoading(false)
    );

    return () => unsubscribe();
  }, []);

  const toggleBan = async (user: User) => {
    setUpdatingUser(user.uid);
    try {
      await updateDoc(doc(db, "users", user.uid), {
        isBanned: !user.isBanned,
      });
      toast.success(user.isBanned ? "User unbanned" : "User banned");
    } catch (error: unknown) {
      const errorMessage = error instanceof Error ? error.message : "Failed to update user";
      toast.error(errorMessage);
    } finally {
      setUpdatingUser(null);
    }
  };

  const toggleAdmin = async (user: User) => {
    setUpdatingUser(user.uid);
    try {
      await updateDoc(doc(db, "users", user.uid), {
        isAdmin: !user.isAdmin,
      });
      toast.success(user.isAdmin ? "Admin removed" : "Admin granted");
    } catch (error: unknown) {
      const errorMessage = error instanceof Error ? error.message : "Failed to update user";
      toast.error(errorMessage);
    } finally {
      setUpdatingUser(null);
    }
  };

  const copyUserId = async (uid: string) => {
    await navigator.clipboard.writeText(uid);
    toast.success("User ID copied");
  };

  const openPointsDialog = (user: User, type: "add" | "deduct") => {
    setPointsDialog({ open: true, type, user });
    setPointsAmount("");
  };

  const closePointsDialog = () => {
    setPointsDialog({ open: false, type: "add", user: null });
    setPointsAmount("");
  };

  const handlePointsAction = async () => {
    const amount = parseInt(pointsAmount);
    if (isNaN(amount) || amount <= 0 || !pointsDialog.user) {
      toast.error("Please enter a valid amount");
      return;
    }

    setUpdatingUser(pointsDialog.user.uid);
    try {
      const pointsChange = pointsDialog.type === "add" ? amount : -amount;
      const userRef = doc(db, "users", pointsDialog.user.uid);
      
      await updateDoc(userRef, {
        points: increment(pointsChange),
        ...(pointsDialog.type === "add" ? { totalEarned: increment(amount) } : {}),
      });

      toast.success(
        pointsDialog.type === "add"
          ? `Added ${amount.toLocaleString()} points to ${pointsDialog.user.username}`
          : `Deducted ${amount.toLocaleString()} points from ${pointsDialog.user.username}`
      );
      closePointsDialog();
    } catch (error: unknown) {
      const errorMessage = error instanceof Error ? error.message : "Failed to update points";
      toast.error(errorMessage);
    } finally {
      setUpdatingUser(null);
    }
  };

  const filteredUsers = users.filter(
    (user) =>
     (user.username || "").toLowerCase().includes(search.toLowerCase()) ||
      (user.email || "").toLowerCase().includes(search.toLowerCase()) ||
      (user.uid || "").toLowerCase().includes(search.toLowerCase())
  );

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
              <Users className="h-6 w-6 text-[#3B82F6]" />
              User Management
            </h1>
            <p className="text-white/50">
              View and manage all registered users
            </p>
          </div>
        </div>

        {/* Search */}
        <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
          <CardContent className="p-4">
            <div className="relative">
              <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white/40" />
              <Input
                placeholder="Search by username, email, or ID..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                className="pl-10 bg-white/5 border-white/10 text-white placeholder:text-white/40"
              />
            </div>
          </CardContent>
        </Card>

        {/* Stats */}
        <div className="grid gap-4 sm:grid-cols-3">
          <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
            <CardContent className="p-5 text-center">
              <p className="text-3xl font-black text-[#3B82F6]">{users.length}</p>
              <p className="text-sm text-white/50">Total Users</p>
            </CardContent>
          </Card>
          <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
            <CardContent className="p-5 text-center">
              <p className="text-3xl font-black text-emerald-500">
                {users.filter((u) => !u.isBanned).length}
              </p>
              <p className="text-sm text-white/50">Active Users</p>
            </CardContent>
          </Card>
          <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
            <CardContent className="p-5 text-center">
              <p className="text-3xl font-black text-red-500">
                {users.filter((u) => u.isBanned).length}
              </p>
              <p className="text-sm text-white/50">Banned Users</p>
            </CardContent>
          </Card>
        </div>

        {/* Users List */}
        <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
          <CardHeader>
            <CardTitle className="text-white">All Users</CardTitle>
            <CardDescription className="text-white/40">
              Showing {filteredUsers.length} of {users.length} users
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
            ) : filteredUsers.length === 0 ? (
              <p className="py-8 text-center text-white/40">
                No users found
              </p>
            ) : (
              <div className="space-y-3">
                {filteredUsers.map((user) => (
                  <div
                    key={user.uid}
                    className={`rounded-2xl border p-5 transition-colors ${
                      user.isBanned
                        ? "border-red-500/30 bg-red-500/5"
                        : "border-white/5 bg-white/[0.02]"
                    }`}
                  >
                    <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                      <div className="flex-1 space-y-1">
                        <div className="flex items-center gap-2">
                          <span className="font-bold text-white">{user.username || "Unknown User"}</span>
                          {user.isAdmin && (
                            <Badge className="bg-[#3B82F6]/10 text-[#3B82F6] border border-[#3B82F6]/20 rounded-xl text-xs">
                              <Shield className="mr-1 h-3 w-3" />
                              Admin
                            </Badge>
                          )}
                          {user.isBanned && (
                            <Badge className="bg-red-500/10 text-red-500 border border-red-500/20 rounded-xl text-xs">
                              <Ban className="mr-1 h-3 w-3" />
                              Banned
                            </Badge>
                          )}
                        </div>
                        <p className="text-sm text-white/50">{user.email || "No Email Provided"}</p>
                        <div className="flex items-center gap-2">
                          <span className="font-mono text-xs text-white/40">
                            {user.uid.slice(0, 12)}...
                          </span>
                          <Button
                            variant="ghost"
                            size="icon"
                            className="h-6 w-6 hover:bg-white/5"
                            onClick={() => copyUserId(user.uid)}
                          >
                            <Copy className="h-3 w-3 text-white/40" />
                          </Button>
                        </div>
                      </div>

                      <div className="flex items-center gap-6">
                        <div className="grid grid-cols-3 gap-4 text-center text-sm">
                          <div>
                            <p className="font-bold text-[#3B82F6]">
                              {user.points?.toLocaleString() ?? "0"}
                            </p>
                            <p className="text-xs text-white/40">Points</p>
                          </div>
                          <div>
                            <p className="font-bold text-[#8B5CF6]">
                              {user.fragments?.toLocaleString() ?? "0"}
                            </p>
                            <p className="text-xs text-white/40">Fragments</p>
                          </div>
                          <div>
                            <p className="font-bold text-amber-500">Lv.{user.level}</p>
                            <p className="text-xs text-white/40">Level</p>
                          </div>
                        </div>

                        <div className="flex flex-wrap items-center gap-2">
                          {/* Add/Deduct Points Buttons */}
                          <Button
                            variant="outline"
                            size="sm"
                            onClick={() => openPointsDialog(user, "add")}
                            disabled={updatingUser === user.uid}
                            className="text-emerald-500 border-emerald-500/30 hover:bg-emerald-500/10 rounded-xl"
                          >
                            <Plus className="mr-1 h-4 w-4" />
                            Add
                          </Button>
                          <Button
                            variant="outline"
                            size="sm"
                            onClick={() => openPointsDialog(user, "deduct")}
                            disabled={updatingUser === user.uid}
                            className="text-orange-500 border-orange-500/30 hover:bg-orange-500/10 rounded-xl"
                          >
                            <Minus className="mr-1 h-4 w-4" />
                            Deduct
                          </Button>
                          
                          <div className="flex items-center gap-2 ml-2">
                            <span className="text-xs text-white/40">Admin</span>
                            <Switch
                              checked={user.isAdmin}
                              onCheckedChange={() => toggleAdmin(user)}
                              disabled={updatingUser === user.uid}
                            />
                          </div>
                          <Button
                            variant={user.isBanned ? "outline" : "destructive"}
                            size="sm"
                            onClick={() => toggleBan(user)}
                            disabled={updatingUser === user.uid}
                            className="rounded-xl"
                          >
                            {user.isBanned ? (
                              <>
                                <CheckCircle className="mr-1 h-4 w-4" />
                                Unban
                              </>
                            ) : (
                              <>
                                <Ban className="mr-1 h-4 w-4" />
                                Ban
                              </>
                            )}
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

        {/* Points Dialog */}
        <Dialog open={pointsDialog.open} onOpenChange={closePointsDialog}>
          <DialogContent className="sm:max-w-md bg-[#0a0a0a] border-white/10">
            <DialogHeader>
              <DialogTitle className="flex items-center gap-2 text-white">
                {pointsDialog.type === "add" ? (
                  <>
                    <Plus className="h-5 w-5 text-emerald-500" />
                    Add Points
                  </>
                ) : (
                  <>
                    <Minus className="h-5 w-5 text-orange-500" />
                    Deduct Points
                  </>
                )}
              </DialogTitle>
              <DialogDescription className="text-white/50">
                {pointsDialog.type === "add" ? "Add" : "Deduct"} points{" "}
                {pointsDialog.type === "add" ? "to" : "from"}{" "}
                <span className="font-semibold text-white">
                  {pointsDialog.user?.username}
                </span>
              </DialogDescription>
            </DialogHeader>

            <div className="space-y-4 py-4">
              <div className="flex items-center gap-2 rounded-xl bg-white/5 p-3">
                <span className="text-sm text-white/50">Current Balance:</span>
                <span className="font-bold text-[#3B82F6]">
                  {pointsDialog.user?.points?.toLocaleString() || 0} points
                </span>
              </div>
              
              <div>
                <label className="text-sm font-medium mb-2 block text-white">
                  Amount to {pointsDialog.type === "add" ? "add" : "deduct"}
                </label>
                <Input
                  type="number"
                  placeholder="Enter points amount"
                  value={pointsAmount}
                  onChange={(e) => setPointsAmount(e.target.value)}
                  min="1"
                  className="bg-white/5 border-white/10 text-white"
                />
              </div>

              {pointsAmount && !isNaN(parseInt(pointsAmount)) && parseInt(pointsAmount) > 0 && (
                <div className="rounded-xl border border-white/10 p-3">
                  <p className="text-sm text-white/50">New Balance:</p>
                  <p className={`text-lg font-bold ${pointsDialog.type === "add" ? "text-emerald-500" : "text-orange-500"}`}>
                    {(
                      (pointsDialog.user?.points || 0) +
                      (pointsDialog.type === "add" ? parseInt(pointsAmount) : -parseInt(pointsAmount))
                    ).toLocaleString()}{" "}
                    points
                  </p>
                </div>
              )}
            </div>

            <DialogFooter>
              <Button variant="outline" onClick={closePointsDialog} className="rounded-xl border-white/10 text-white hover:bg-white/5">
                Cancel
              </Button>
              <Button
                onClick={handlePointsAction}
                disabled={updatingUser === pointsDialog.user?.uid}
                className={`rounded-xl ${
                  pointsDialog.type === "add"
                    ? "bg-emerald-500 hover:bg-emerald-600 text-white"
                    : "bg-orange-500 hover:bg-orange-600 text-white"
                }`}
              >
                {updatingUser === pointsDialog.user?.uid ? (
                  <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                ) : pointsDialog.type === "add" ? (
                  <Plus className="mr-2 h-4 w-4" />
                ) : (
                  <Minus className="mr-2 h-4 w-4" />
                )}
                {pointsDialog.type === "add" ? "Add Points" : "Deduct Points"}
              </Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>
      </div>
    </div>
  );
}
