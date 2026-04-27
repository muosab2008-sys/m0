"use client";

import { useAuth } from "@/contexts/auth-context";
import { Button } from "@/components/ui/button";
import { Menu, LogOut, User } from "lucide-react";

interface HeaderProps {
  onMenuClick: () => void;
}

export function AdminHeader({ onMenuClick }: HeaderProps) {
  const { userData, signOut } = useAuth();

  return (
    <header className="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-white/5 bg-[#0a0a0a]/95 backdrop-blur px-4 lg:px-6">
      <div className="flex items-center gap-4">
        <Button
          variant="ghost"
          size="icon"
          className="lg:hidden"
          onClick={onMenuClick}
        >
          <Menu className="h-5 w-5 text-white" />
        </Button>
      </div>

      <div className="flex items-center gap-4">
        <div className="flex items-center gap-3 rounded-full bg-white/5 px-4 py-2">
          <div className="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-[#3B82F6] to-[#8B5CF6]">
            <User className="h-4 w-4 text-white" />
          </div>
          <div className="hidden sm:block">
            <p className="text-sm font-medium text-white">{userData?.username || "Admin"}</p>
            <p className="text-xs text-white/50">{userData?.email}</p>
          </div>
        </div>
        <Button
          variant="ghost"
          size="icon"
          onClick={() => signOut()}
          className="text-white/70 hover:text-white hover:bg-white/5"
        >
          <LogOut className="h-5 w-5" />
        </Button>
      </div>
    </header>
  );
}
