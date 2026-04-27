"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { cn } from "@/lib/utils";
import {
  LayoutDashboard,
  Users,
  Wallet,
  Gift,
  Ticket,
  Globe,
  Settings,
  LogOut,
  DollarSign,
} from "lucide-react";

const navItems = [
  { href: "/admin", icon: LayoutDashboard, label: "Dashboard" },
  { href: "/admin/users", icon: Users, label: "Users" },
  { href: "/admin/withdrawals", icon: Wallet, label: "Withdrawals" },
  { href: "/admin/giveaways", icon: Gift, label: "Giveaways" },
  { href: "/admin/promo", icon: Ticket, label: "Promo Codes" },
  { href: "/admin/offerwalls", icon: Globe, label: "Offerwalls" },
  { href: "/admin/settings", icon: Settings, label: "Settings" },
];

export function Sidebar() {
  const pathname = usePathname();

  return (
    <aside className="fixed top-0 left-0 z-40 h-screen w-64 border-r border-[var(--border)] bg-[var(--card)]">
      <div className="flex h-full flex-col">
        {/* Logo */}
        <div className="flex h-16 items-center gap-3 border-b border-[var(--border)] px-6">
          <div className="flex h-10 w-10 items-center justify-center rounded-xl gradient-primary">
            <DollarSign className="h-6 w-6 text-white" />
          </div>
          <span className="text-xl font-bold gradient-text">MrCash</span>
        </div>

        {/* Navigation */}
        <nav className="flex-1 space-y-1 p-4">
          {navItems.map((item) => {
            const isActive = pathname === item.href || 
              (item.href !== "/admin" && pathname.startsWith(item.href));
            return (
              <Link
                key={item.href}
                href={item.href}
                className={cn(
                  "flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200",
                  isActive
                    ? "gradient-primary text-white shadow-lg shadow-blue-500/25"
                    : "text-[var(--muted-foreground)] hover:bg-[var(--secondary)] hover:text-[var(--foreground)]"
                )}
              >
                <item.icon className="h-5 w-5" />
                {item.label}
              </Link>
            );
          })}
        </nav>

        {/* Logout */}
        <div className="border-t border-[var(--border)] p-4">
          <button className="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-[var(--destructive)] transition-all duration-200 hover:bg-[var(--destructive)]/10">
            <LogOut className="h-5 w-5" />
            Logout
          </button>
        </div>
      </div>
    </aside>
  );
}
