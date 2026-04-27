"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useAuth } from "@/contexts/auth-context";
import { Loader2 } from "lucide-react";

export default function Home() {
  const router = useRouter();
  const { user, userData, loading } = useAuth();

  useEffect(() => {
    if (!loading) {
      if (user && userData?.isAdmin) {
        router.push("/admin");
      } else if (user) {
        // If user is not admin, redirect to login
        router.push("/login");
      } else {
        router.push("/login");
      }
    }
  }, [user, userData, loading, router]);

  return (
    <div className="min-h-screen bg-background flex items-center justify-center">
      <div className="text-center">
        <Loader2 className="mx-auto h-8 w-8 animate-spin text-primary" />
        <p className="mt-2 text-sm text-white/50">Loading...</p>
      </div>
    </div>
  );
}
