"use client";

import { useState } from "react";
import { AuthGuard } from "@/components/auth/auth-guard";
import { AdminSidebar } from "@/components/admin/sidebar";
import { AdminHeader } from "@/components/admin/header";

export default function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const [sidebarOpen, setSidebarOpen] = useState(false);

  return (
    <AuthGuard requireAdmin>
      <div className="min-h-screen bg-[#0a0a0a]">
        <AdminSidebar open={sidebarOpen} onClose={() => setSidebarOpen(false)} />
        <div className="lg:pl-64">
          <AdminHeader onMenuClick={() => setSidebarOpen(true)} />
          <main className="p-4 sm:p-6">{children}</main>
        </div>
      </div>
    </AuthGuard>
  );
}
