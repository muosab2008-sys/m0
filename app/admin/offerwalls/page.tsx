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
  Loader2,
  ExternalLink,
  Zap,
  Copy,
} from "lucide-react";

interface Offerwall {
  id: string;
  name: string;
  description: string;
  url: string;
  apiKey: string;
  secretKey: string;
  pointsMultiplier: number;
  color: string;
  isActive: boolean;
  createdAt: Date;
}

const defaultOfferwalls = [
  { id: "lootably", name: "Lootably", color: "#10B981" },
  { id: "offertoro", name: "OfferToro", color: "#3B82F6" },
  { id: "adgatemedia", name: "AdGateMedia", color: "#8B5CF6" },
  { id: "cpxresearch", name: "CPX Research", color: "#F59E0B" },
  { id: "bitlabs", name: "BitLabs", color: "#EC4899" },
  { id: "timewall", name: "Timewall", color: "#6366F1" },
  { id: "notik", name: "Notik", color: "#14B8A6" },
  { id: "monlix", name: "Monlix", color: "#F97316" },
  { id: "wannads", name: "Wannads", color: "#84CC16" },
];

export default function AdminOfferwallsPage() {
  const [offerwalls, setOfferwalls] = useState<Offerwall[]>([]);
  const [loading, setLoading] = useState(true);
  const [creating, setCreating] = useState(false);
  const [showForm, setShowForm] = useState(false);
  
  const [formData, setFormData] = useState({
    name: "",
    description: "",
    url: "",
    apiKey: "",
    secretKey: "",
    pointsMultiplier: "1",
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
            description: d.description || "",
            url: d.url || "",
            apiKey: d.apiKey || "",
            secretKey: d.secretKey || "",
            pointsMultiplier: d.pointsMultiplier || 1,
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
    if (!formData.name.trim()) {
      toast.error("Please enter a name");
      return;
    }

    setCreating(true);
    try {
      const id = formData.name.toLowerCase().replace(/\s+/g, "-");
      await setDoc(doc(db, "offerwalls", id), {
        name: formData.name,
        description: formData.description,
        url: formData.url,
        apiKey: formData.apiKey,
        secretKey: formData.secretKey,
        pointsMultiplier: parseFloat(formData.pointsMultiplier) || 1,
        color: formData.color,
        isActive: true,
        createdAt: serverTimestamp(),
      });

      toast.success("Offerwall created!");
      setFormData({
        name: "",
        description: "",
        url: "",
        apiKey: "",
        secretKey: "",
        pointsMultiplier: "1",
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

  const updateMultiplier = async (wall: Offerwall, value: string) => {
    const num = parseFloat(value);
    if (isNaN(num) || num < 0) return;

    try {
      await updateDoc(doc(db, "offerwalls", wall.id), {
        pointsMultiplier: num,
      });
    } catch (error: unknown) {
      const errorMessage = error instanceof Error ? error.message : "Failed to update";
      toast.error(errorMessage);
    }
  };

  const deleteOfferwall = async (id: string) => {
    if (!confirm("Are you sure you want to delete this offerwall?")) return;
    
    try {
      await deleteDoc(doc(db, "offerwalls", id));
      toast.success("Offerwall deleted");
    } catch (error: unknown) {
      const errorMessage = error instanceof Error ? error.message : "Failed to delete offerwall";
      toast.error(errorMessage);
    }
  };

  const copyPostbackUrl = async (wallId: string) => {
    const baseUrl = typeof window !== "undefined" ? window.location.origin : "";
    const url = `${baseUrl}/api/postback?wall=${wallId}&user_id={user_id}&points={points}&txid={transaction_id}`;
    await navigator.clipboard.writeText(url);
    toast.success("Postback URL copied!");
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold flex items-center gap-2 text-white">
            <Settings className="h-6 w-6 text-[#3B82F6]" />
            Offerwalls
          </h1>
          <p className="text-white/50">
            Configure offerwall integrations
          </p>
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
          <code className="block rounded-xl bg-black p-4 text-xs text-white/70 break-all">
            {typeof window !== "undefined" ? window.location.origin : ""}/api/postback?wall=WALL_NAME&user_id={"{user_id}"}&points={"{points}"}&txid={"{transaction_id}"}
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
              />
              <Input
                placeholder="Description"
                value={formData.description}
                onChange={(e) => setFormData({ ...formData, description: e.target.value })}
              />
              <Input
                placeholder="Offerwall URL"
                value={formData.url}
                onChange={(e) => setFormData({ ...formData, url: e.target.value })}
              />
              <Input
                placeholder="API Key"
                value={formData.apiKey}
                onChange={(e) => setFormData({ ...formData, apiKey: e.target.value })}
              />
              <Input
                placeholder="Secret Key"
                value={formData.secretKey}
                onChange={(e) => setFormData({ ...formData, secretKey: e.target.value })}
              />
              <Input
                type="number"
                step="0.1"
                placeholder="Points Multiplier"
                value={formData.pointsMultiplier}
                onChange={(e) => setFormData({ ...formData, pointsMultiplier: e.target.value })}
              />
              <div className="flex items-center gap-2">
                <Input
                  type="color"
                  value={formData.color}
                  onChange={(e) => setFormData({ ...formData, color: e.target.value })}
                  className="h-10 w-20 p-1"
                />
                <span className="text-sm text-white/50">Brand Color</span>
              </div>
            </div>
            <div className="flex gap-2">
              <Button variant="outline" onClick={() => setShowForm(false)}>
                Cancel
              </Button>
              <Button
                onClick={createOfferwall}
                disabled={creating}
                className="bg-gradient-to-r from-[#3B82F6] to-[#8B5CF6] text-white"
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

      {/* Quick Add Section */}
      <Card className="border-white/5 bg-[#0a0a0a] rounded-2xl">
        <CardHeader>
          <CardTitle className="text-white">Quick Add Popular Offerwalls</CardTitle>
          <CardDescription className="text-white/40">Click to add pre-configured offerwall</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="flex flex-wrap gap-2">
            {defaultOfferwalls.map((wall) => {
              const exists = offerwalls.some((o) => o.id === wall.id);
              return (
                <Button
                  key={wall.id}
                  variant="outline"
                  size="sm"
                  disabled={exists}
                  onClick={async () => {
                    try {
                      await setDoc(doc(db, "offerwalls", wall.id), {
                        name: wall.name,
                        description: "",
                        url: "",
                        apiKey: "",
                        secretKey: "",
                        pointsMultiplier: 1,
                        color: wall.color,
                        isActive: false,
                        createdAt: serverTimestamp(),
                      });
                      toast.success(`${wall.name} added!`);
                    } catch (error) {
                      toast.error("Failed to add offerwall");
                    }
                  }}
                  className="rounded-xl"
                  style={{ borderColor: wall.color + "50" }}
                >
                  <div
                    className="h-3 w-3 rounded-full mr-2"
                    style={{ backgroundColor: wall.color }}
                  />
                  {wall.name}
                  {exists && " (Added)"}
                </Button>
              );
            })}
          </div>
        </CardContent>
      </Card>

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
                  className="flex items-center justify-between rounded-2xl bg-white/5 p-4 animate-pulse"
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
                  className={`rounded-2xl border p-4 ${
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
                            <Badge variant="secondary" className="text-xs">Disabled</Badge>
                          )}
                        </div>
                        <p className="text-sm text-white/40">
                          {wall.description || "No description"}
                        </p>
                      </div>
                    </div>

                    <div className="flex items-center gap-4 flex-wrap">
                      <div className="flex items-center gap-2">
                        <Zap className="h-4 w-4 text-[#8B5CF6]" />
                        <Input
                          type="number"
                          step="0.1"
                          value={wall.pointsMultiplier}
                          onChange={(e) => updateMultiplier(wall, e.target.value)}
                          className="w-20 h-8 text-center"
                        />
                        <span className="text-xs text-white/40">
                          multiplier
                        </span>
                      </div>

                      <Button
                        variant="ghost"
                        size="sm"
                        onClick={() => copyPostbackUrl(wall.id)}
                        className="text-white/50 hover:text-white"
                      >
                        <Copy className="h-4 w-4 mr-1" />
                        Postback
                      </Button>

                      {wall.url && (
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => window.open(wall.url, "_blank")}
                          className="text-white/50 hover:text-white"
                        >
                          <ExternalLink className="h-4 w-4" />
                        </Button>
                      )}

                      <Switch
                        checked={wall.isActive}
                        onCheckedChange={() => toggleActive(wall)}
                      />

                      <Button
                        variant="ghost"
                        size="icon"
                        className="text-red-500 hover:text-red-400 hover:bg-red-500/10"
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
  );
}
