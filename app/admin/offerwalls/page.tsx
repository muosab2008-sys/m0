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
  Settings,
  Plus,
  Trash2,
  ArrowLeft,
  Loader2,
  ExternalLink,
  Zap,
} from "lucide-react";
import Link from "next/link";

interface Offerwall {
  id: string;
  name: string;
  description: string;
  url: string;
  postbackUrl: string;
  pointsPerFragment: number;
  avgPoints: number;
  color: string;
  isActive: boolean;
  createdAt: Date;
}

export default function AdminOfferwallsPage() {
  const [offerwalls, setOfferwalls] = useState<Offerwall[]>([]);
  const [loading, setLoading] = useState(true);
  const [creating, setCreating] = useState(false);
  const [showForm, setShowForm] = useState(false);
  
  // Form state
  const [formData, setFormData] = useState({
    name: "",
    description: "",
    url: "",
    postbackUrl: "",
    pointsPerFragment: "10",
    avgPoints: "500",
    color: "#3B82F6",
  });

  useEffect(() => {
    const q = query(collection(db, "offerwalls"), orderBy("createdAt", "desc"));

    const unsubscribe = onSnapshot(
      q,
      (snapshot) => {
        const data = snapshot.docs.map((doc) => {
          const d = doc.data();
          return {
            id: doc.id,
            name: d.name,
            description: d.description,
            url: d.url,
            postbackUrl: d.postbackUrl || "",
            pointsPerFragment: d.pointsPerFragment || 10,
            avgPoints: d.avgPoints || 500,
            color: d.color || "#3B82F6",
            isActive: d.isActive ?? true,
            createdAt: d.createdAt?.toDate() || new Date(),
          };
        }) as Offerwall[];
        setOfferwalls(data);
        setLoading(false);
      },
      () => setLoading(false)
    );

    return () => unsubscribe();
  }, []);

  const createOfferwall = async () => {
    if (!formData.name.trim() || !formData.url.trim()) {
      toast.error("Please fill in name and URL");
      return;
    }

    setCreating(true);
    try {
      const id = formData.name.toLowerCase().replace(/\s+/g, "-");
      await setDoc(doc(db, "offerwalls", id), {
        name: formData.name,
        description: formData.description,
        url: formData.url,
        postbackUrl: formData.postbackUrl,
        pointsPerFragment: parseInt(formData.pointsPerFragment) || 10,
        avgPoints: parseInt(formData.avgPoints) || 500,
        color: formData.color,
        isActive: true,
        createdAt: serverTimestamp(),
      });

      toast.success("Offerwall created!");
      setFormData({
        name: "",
        description: "",
        url: "",
        postbackUrl: "",
        pointsPerFragment: "10",
        avgPoints: "500",
        color: "#3B82F6",
      });
      setShowForm(false);
    } catch (error: unknown) {
      const errorMessage = error instanceof Error ? error.message : "Failed to create offerwall";
      toast.error(errorMessage);
    } finally {
      setCreating(false);
    }
  };

  const toggleActive = async (wall: Offerwall) => {
    try {
      await updateDoc(doc(db, "offerwalls", wall.id), {
        isActive: !wall.isActive,
      });
      toast.success(wall.isActive ? "Offerwall disabled" : "Offerwall enabled");
    } catch (error: unknown) {
      const errorMessage = error instanceof Error ? error.message : "Failed to update offerwall";
      toast.error(errorMessage);
    }
  };

  const updateFragments = async (wall: Offerwall, value: string) => {
    const num = parseInt(value);
    if (isNaN(num) || num < 0) return;

    try {
      await updateDoc(doc(db, "offerwalls", wall.id), {
        pointsPerFragment: num,
      });
    } catch (error: unknown) {
      const errorMessage = error instanceof Error ? error.message : "Failed to update";
      toast.error(errorMessage);
    }
  };

  const deleteOfferwall = async (id: string) => {
    try {
      await deleteDoc(doc(db, "offerwalls", id));
      toast.success("Offerwall deleted");
    } catch (error: unknown) {
      const errorMessage = error instanceof Error ? error.message : "Failed to delete offerwall";
      toast.error(errorMessage);
    }
  };

  // Generate postback URL
  const baseUrl = typeof window !== "undefined" ? window.location.origin : "";
  const postbackExample = `${baseUrl}/api/postback?userId={user_id}&points={points}&offerwall={offerwall_name}&txid={transaction_id}`;

  return (
    <div className="min-h-screen bg-background">
      <div className="mx-auto max-w-7xl space-y-6 p-4 sm:p-6">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-4">
            <Link href="/admin">
              <Button variant="ghost" size="icon" className="rounded-xl hover:bg-white/5">
                <ArrowLeft className="h-5 w-5 text-white" />
              </Button>
            </Link>
            <div>
              <h1 className="text-2xl font-bold flex items-center gap-2 text-white">
                <Settings className="h-6 w-6 text-[#3B82F6]" />
                Offerwalls
              </h1>
              <p className="text-white/50">
                Configure offerwall integrations
              </p>
            </div>
          </div>
          <Button
            onClick={() => setShowForm(!showForm)}
            className="bg-gradient-to-r from-[#3B82F6] to-[#8B5CF6] text-white rounded-xl"
          >
            <Plus className="mr-2 h-4 w-4" />
            Add Offerwall
          </Button>
        </div>

        {/* Postback URL Info */}
        <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
          <CardHeader>
            <CardTitle className="text-sm text-white">Postback URL Format</CardTitle>
          </CardHeader>
          <CardContent>
            <code className="block rounded-xl bg-white/5 p-3 text-xs text-white/70 break-all">
              {postbackExample}
            </code>
            <p className="mt-2 text-xs text-white/40">
              Use this format when setting up postbacks in your offerwall provider dashboard.
            </p>
          </CardContent>
        </Card>

        {/* Create Form */}
        {showForm && (
          <Card className="border-[#3B82F6]/30 bg-[#0a0a0a] rounded-2xl">
            <CardHeader>
              <CardTitle className="text-white">Add New Offerwall</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <Input
                  placeholder="Name (e.g., OfferToro)"
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  className="bg-white/5 border-white/10 text-white placeholder:text-white/40"
                />
                <Input
                  placeholder="Description"
                  value={formData.description}
                  onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                  className="bg-white/5 border-white/10 text-white placeholder:text-white/40"
                />
                <Input
                  placeholder="Offerwall URL"
                  value={formData.url}
                  onChange={(e) => setFormData({ ...formData, url: e.target.value })}
                  className="bg-white/5 border-white/10 text-white placeholder:text-white/40"
                />
                <Input
                  placeholder="Average Points per Offer"
                  type="number"
                  value={formData.avgPoints}
                  onChange={(e) => setFormData({ ...formData, avgPoints: e.target.value })}
                  className="bg-white/5 border-white/10 text-white placeholder:text-white/40"
                />
                <Input
                  placeholder="Fragments per Task"
                  type="number"
                  value={formData.pointsPerFragment}
                  onChange={(e) => setFormData({ ...formData, pointsPerFragment: e.target.value })}
                  className="bg-white/5 border-white/10 text-white placeholder:text-white/40"
                />
                <div className="flex items-center gap-2">
                  <Input
                    type="color"
                    value={formData.color}
                    onChange={(e) => setFormData({ ...formData, color: e.target.value })}
                    className="h-10 w-20 bg-transparent border-white/10"
                  />
                  <span className="text-sm text-white/50">Brand Color</span>
                </div>
              </div>
              <div className="flex gap-2">
                <Button
                  variant="outline"
                  onClick={() => setShowForm(false)}
                  className="rounded-xl border-white/10 text-white hover:bg-white/5"
                >
                  Cancel
                </Button>
                <Button
                  onClick={createOfferwall}
                  disabled={creating}
                  className="bg-gradient-to-r from-[#3B82F6] to-[#8B5CF6] text-white rounded-xl"
                >
                  {creating ? (
                    <Loader2 className="h-4 w-4 animate-spin" />
                  ) : (
                    "Create Offerwall"
                  )}
                </Button>
              </div>
            </CardContent>
          </Card>
        )}

        {/* Offerwalls List */}
        <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
          <CardHeader>
            <CardTitle className="text-white">Configured Offerwalls</CardTitle>
            <CardDescription className="text-white/40">{offerwalls.length} offerwalls</CardDescription>
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
            ) : offerwalls.length === 0 ? (
              <p className="py-8 text-center text-white/40">
                No offerwalls configured yet
              </p>
            ) : (
              <div className="space-y-3">
                {offerwalls.map((wall) => (
                  <div
                    key={wall.id}
                    className={`rounded-2xl border p-5 ${
                      wall.isActive
                        ? "border-white/10 bg-white/[0.02]"
                        : "border-white/5 bg-white/[0.01] opacity-60"
                    }`}
                  >
                    <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                      <div className="flex items-center gap-3">
                        <div
                          className="h-10 w-10 rounded-xl flex items-center justify-center text-white font-bold"
                          style={{ backgroundColor: wall.color }}
                        >
                          {wall.name.charAt(0)}
                        </div>
                        <div>
                          <div className="flex items-center gap-2">
                            <span className="font-bold text-white">{wall.name}</span>
                            {!wall.isActive && (
                              <Badge variant="secondary" className="bg-white/10 text-white/50">Disabled</Badge>
                            )}
                          </div>
                          <p className="text-sm text-white/50">
                            {wall.description}
                          </p>
                        </div>
                      </div>

                      <div className="flex items-center gap-4">
                        <div className="flex items-center gap-2">
                          <Zap className="h-4 w-4 text-[#8B5CF6]" />
                          <Input
                            type="number"
                            value={wall.pointsPerFragment}
                            onChange={(e) => updateFragments(wall, e.target.value)}
                            className="w-20 h-8 text-center bg-white/5 border-white/10 text-white"
                          />
                          <span className="text-xs text-white/40">
                            frags/task
                          </span>
                        </div>

                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => window.open(wall.url, "_blank")}
                          className="hover:bg-white/5"
                        >
                          <ExternalLink className="h-4 w-4 text-white/40" />
                        </Button>

                        <Switch
                          checked={wall.isActive}
                          onCheckedChange={() => toggleActive(wall)}
                        />

                        <Button
                          variant="ghost"
                          size="icon"
                          className="text-red-500 hover:text-red-400 hover:bg-red-500/10 rounded-xl"
                          onClick={() => deleteOfferwall(wall.id)}
                        >
                          <Trash2 className="h-4 w-4" />
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
    </div>
  );
}
