"use client";

import { Header } from "@/components/admin/header";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { formatNumber } from "@/lib/utils";
import {
  Users,
  Wallet,
  TrendingUp,
  DollarSign,
  ArrowUpRight,
  ArrowDownRight,
  Clock,
  CheckCircle,
  XCircle,
} from "lucide-react";
import {
  LineChart,
  Line,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  AreaChart,
  Area,
} from "recharts";

// Demo data
const stats = [
  {
    title: "Total Users",
    value: 12847,
    change: +12.5,
    icon: Users,
    color: "from-blue-500 to-blue-600",
  },
  {
    title: "Total Points",
    value: 8459230,
    change: +8.2,
    icon: DollarSign,
    color: "from-purple-500 to-purple-600",
  },
  {
    title: "Pending Withdrawals",
    value: 156,
    change: -3.1,
    icon: Wallet,
    color: "from-amber-500 to-amber-600",
  },
  {
    title: "Today's Revenue",
    value: 4521,
    change: +15.3,
    icon: TrendingUp,
    color: "from-green-500 to-green-600",
  },
];

const chartData = [
  { name: "Mon", users: 400, points: 2400 },
  { name: "Tue", users: 300, points: 1398 },
  { name: "Wed", users: 520, points: 9800 },
  { name: "Thu", users: 278, points: 3908 },
  { name: "Fri", users: 189, points: 4800 },
  { name: "Sat", users: 239, points: 3800 },
  { name: "Sun", users: 349, points: 4300 },
];

const recentWithdrawals = [
  { id: 1, user: "Ahmed Mohamed", amount: 50, method: "PayPal", status: "pending", time: "2 min ago" },
  { id: 2, user: "Sara Ali", amount: 25, method: "Vodafone Cash", status: "completed", time: "15 min ago" },
  { id: 3, user: "Mohamed Hassan", amount: 100, method: "Bank Transfer", status: "pending", time: "1 hour ago" },
  { id: 4, user: "Fatima Omar", amount: 30, method: "PayPal", status: "rejected", time: "2 hours ago" },
  { id: 5, user: "Khaled Ibrahim", amount: 75, method: "Instapay", status: "completed", time: "3 hours ago" },
];

const topUsers = [
  { id: 1, name: "Ahmed Mohamed", points: 125000, referrals: 45 },
  { id: 2, name: "Sara Ali", points: 98500, referrals: 38 },
  { id: 3, name: "Mohamed Hassan", points: 87200, referrals: 32 },
  { id: 4, name: "Fatima Omar", points: 76800, referrals: 28 },
  { id: 5, name: "Khaled Ibrahim", points: 65400, referrals: 25 },
];

export default function AdminDashboard() {
  return (
    <div className="min-h-screen">
      <Header title="Dashboard" description="Welcome back, Admin" />

      <div className="p-6 space-y-6">
        {/* Stats Grid */}
        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
          {stats.map((stat) => (
            <Card key={stat.title} className="overflow-hidden">
              <CardContent className="p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm text-[var(--muted-foreground)]">{stat.title}</p>
                    <p className="mt-2 text-3xl font-bold">{formatNumber(stat.value)}</p>
                    <div className="mt-2 flex items-center gap-1">
                      {stat.change > 0 ? (
                        <ArrowUpRight className="h-4 w-4 text-[var(--success)]" />
                      ) : (
                        <ArrowDownRight className="h-4 w-4 text-[var(--destructive)]" />
                      )}
                      <span
                        className={
                          stat.change > 0
                            ? "text-sm text-[var(--success)]"
                            : "text-sm text-[var(--destructive)]"
                        }
                      >
                        {Math.abs(stat.change)}%
                      </span>
                      <span className="text-sm text-[var(--muted-foreground)]">vs last week</span>
                    </div>
                  </div>
                  <div className={`flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br ${stat.color}`}>
                    <stat.icon className="h-6 w-6 text-white" />
                  </div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        {/* Charts */}
        <div className="grid gap-6 lg:grid-cols-2">
          <Card>
            <CardHeader>
              <CardTitle>User Registrations</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="h-[300px]">
                <ResponsiveContainer width="100%" height="100%">
                  <AreaChart data={chartData}>
                    <defs>
                      <linearGradient id="colorUsers" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="5%" stopColor="#3b82f6" stopOpacity={0.3} />
                        <stop offset="95%" stopColor="#3b82f6" stopOpacity={0} />
                      </linearGradient>
                    </defs>
                    <CartesianGrid strokeDasharray="3 3" stroke="#27272a" />
                    <XAxis dataKey="name" stroke="#71717a" />
                    <YAxis stroke="#71717a" />
                    <Tooltip
                      contentStyle={{
                        backgroundColor: "#111111",
                        border: "1px solid #27272a",
                        borderRadius: "12px",
                      }}
                    />
                    <Area
                      type="monotone"
                      dataKey="users"
                      stroke="#3b82f6"
                      strokeWidth={2}
                      fillOpacity={1}
                      fill="url(#colorUsers)"
                    />
                  </AreaChart>
                </ResponsiveContainer>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Points Distribution</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="h-[300px]">
                <ResponsiveContainer width="100%" height="100%">
                  <LineChart data={chartData}>
                    <CartesianGrid strokeDasharray="3 3" stroke="#27272a" />
                    <XAxis dataKey="name" stroke="#71717a" />
                    <YAxis stroke="#71717a" />
                    <Tooltip
                      contentStyle={{
                        backgroundColor: "#111111",
                        border: "1px solid #27272a",
                        borderRadius: "12px",
                      }}
                    />
                    <Line
                      type="monotone"
                      dataKey="points"
                      stroke="#8b5cf6"
                      strokeWidth={2}
                      dot={{ fill: "#8b5cf6", strokeWidth: 2 }}
                    />
                  </LineChart>
                </ResponsiveContainer>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Tables */}
        <div className="grid gap-6 lg:grid-cols-2">
          {/* Recent Withdrawals */}
          <Card>
            <CardHeader>
              <CardTitle>Recent Withdrawals</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {recentWithdrawals.map((withdrawal) => (
                  <div
                    key={withdrawal.id}
                    className="flex items-center justify-between rounded-xl bg-[var(--secondary)] p-4"
                  >
                    <div className="flex items-center gap-4">
                      <div className="flex h-10 w-10 items-center justify-center rounded-full bg-[var(--muted)]">
                        {withdrawal.user.charAt(0)}
                      </div>
                      <div>
                        <p className="font-medium">{withdrawal.user}</p>
                        <p className="text-sm text-[var(--muted-foreground)]">
                          ${withdrawal.amount} via {withdrawal.method}
                        </p>
                      </div>
                    </div>
                    <div className="text-right">
                      <Badge
                        variant={
                          withdrawal.status === "completed"
                            ? "success"
                            : withdrawal.status === "pending"
                            ? "warning"
                            : "destructive"
                        }
                      >
                        {withdrawal.status === "completed" && <CheckCircle className="mr-1 h-3 w-3" />}
                        {withdrawal.status === "pending" && <Clock className="mr-1 h-3 w-3" />}
                        {withdrawal.status === "rejected" && <XCircle className="mr-1 h-3 w-3" />}
                        {withdrawal.status}
                      </Badge>
                      <p className="mt-1 text-xs text-[var(--muted-foreground)]">{withdrawal.time}</p>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>

          {/* Top Users */}
          <Card>
            <CardHeader>
              <CardTitle>Top Users</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {topUsers.map((user, index) => (
                  <div
                    key={user.id}
                    className="flex items-center justify-between rounded-xl bg-[var(--secondary)] p-4"
                  >
                    <div className="flex items-center gap-4">
                      <div
                        className={`flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold ${
                          index === 0
                            ? "bg-yellow-500 text-black"
                            : index === 1
                            ? "bg-gray-400 text-black"
                            : index === 2
                            ? "bg-amber-700 text-white"
                            : "bg-[var(--muted)] text-[var(--muted-foreground)]"
                        }`}
                      >
                        {index + 1}
                      </div>
                      <div>
                        <p className="font-medium">{user.name}</p>
                        <p className="text-sm text-[var(--muted-foreground)]">
                          {user.referrals} referrals
                        </p>
                      </div>
                    </div>
                    <div className="text-right">
                      <p className="font-semibold gradient-text">{formatNumber(user.points)}</p>
                      <p className="text-xs text-[var(--muted-foreground)]">points</p>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}
