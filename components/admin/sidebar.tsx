"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { cn } from "@/lib/utils";
import {
  LayoutDashboard,
  Users,
  DollarSign,
  Clock,
  CheckCircle,
  XCircle,
  Settings,
  Gift,
  Ticket,
  Trophy,
  Shield,
  FileText,
  Zap,
  X,
} from "lucide-react";
import { Button } from "@/components/ui/button";

interface SidebarProps {
  open: boolean;
  onClose: () => void;
}

const menuItems = [
  {
    title: "Dashboard",
    href: "/admin",
    icon: LayoutDashboard,
  },
  {
    title: "Users",
    section: "Users",
    items: [
      { title: "All Users", href: "/admin/users", icon: Users },
    ],
  },
  {
    title: "Transactions",
    section: "Transactions",
    items: [
      { title: "Completed", href: "/admin/transactions/completed", icon: CheckCircle },
      { title: "Pending", href: "/admin/transactions/pending", icon: Clock },
      { title: "Rejected", href: "/admin/transactions/rejected", icon: XCircle },
    ],
  },
  {
    title: "Withdrawals",
    section: "Withdrawals",
    items: [
      { title: "Pending", href: "/admin/withdrawals", icon: Clock },
      { title: "Completed", href: "/admin/withdrawals/completed", icon: CheckCircle },
    ],
  },
  {
    title: "Website",
    section: "Website",
    items: [
      { title: "Leaderboard", href: "/admin/leaderboard", icon: Trophy },
      { title: "Giveaways", href: "/admin/giveaways", icon: Gift },
      { title: "Promo Codes", href: "/admin/promo", icon: Ticket },
      { title: "FAQ", href: "/admin/faq", icon: FileText },
    ],
  },
  {
    title: "Settings",
    section: "Settings",
    items: [
      { title: "General Settings", href: "/admin/settings", icon: Settings },
      { title: "Offerwalls", href: "/admin/offerwalls", icon: Zap },
    ],
  },
];

export function AdminSidebar({ open, onClose }: SidebarProps) {
  const pathname = usePathname();

  return (
    <>
      {/* Overlay */}
      {open && (
        <div
          className="fixed inset-0 z-40 bg-black/50 lg:hidden"
          onClick={onClose}
        />
      )}

      {/* Sidebar */}
      <aside
        className={cn(
          "fixed left-0 top-0 z-50 h-full w-64 transform overflow-y-auto bg-[#0a0a0a] border-r border-white/5 transition-transform duration-300 lg:translate-x-0",
          open ? "translate-x-0" : "-translate-x-full"
        )}
      >
        {/* Logo */}
        <div className="flex h-16 items-center justify-between px-4 border-b border-white/5">
          <Link href="/admin" className="flex items-center gap-2">
            <Shield className="h-8 w-8 text-[#3B82F6]" />
            <span className="text-xl font-bold text-white">Admin Panel</span>
          </Link>
          <Button
            variant="ghost"
            size="icon"
            className="lg:hidden"
            onClick={onClose}
          >
            <X className="h-5 w-5 text-white" />
          </Button>
        </div>

        {/* Navigation */}
        <nav className="p-4 space-y-2">
          {menuItems.map((item, index) => {
            if (item.href) {
              const isActive = pathname === item.href;
              const Icon = item.icon;
              return (
                <Link
                  key={index}
                  href={item.href}
                  onClick={onClose}
                  className={cn(
                    "flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors",
                    isActive
                      ? "bg-gradient-to-r from-[#3B82F6] to-[#8B5CF6] text-white"
                      : "text-white/70 hover:bg-white/5 hover:text-white"
                  )}
                >
                  <Icon className="h-5 w-5" />
                  {item.title}
                </Link>
              );
            }

            return (
              <div key={index} className="pt-4">
                <p className="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-white/30">
                  {item.section}
                </p>
                <div className="space-y-1">
                  {item.items?.map((subItem, subIndex) => {
                    const isActive = pathname === subItem.href;
                    const Icon = subItem.icon;
                    return (
                      <Link
                        key={subIndex}
                        href={subItem.href}
                        onClick={onClose}
                        className={cn(
                          "flex items-center gap-3 rounded-xl px-3 py-2 text-sm transition-colors",
                          isActive
                            ? "bg-white/10 text-white"
                            : "text-white/50 hover:bg-white/5 hover:text-white"
                        )}
                      >
                        <Icon className="h-4 w-4" />
                        {subItem.title}
                      </Link>
                    );
                  })}
                </div>
              </div>
            );
          })}
        </nav>
      </aside>
    </>
  );
}
