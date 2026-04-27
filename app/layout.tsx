import type { Metadata } from "next";
import { Toaster } from "sonner";
import "./globals.css";

export const metadata: Metadata = {
  title: "MrCash Admin Dashboard",
  description: "Professional rewards platform administration",
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en" className="bg-[#0a0a0a]">
      <body className="antialiased">
        {children}
        <Toaster
          theme="dark"
          position="top-center"
          toastOptions={{
            style: {
              background: "#111111",
              border: "1px solid #27272a",
              color: "#ffffff",
            },
          }}
        />
      </body>
    </html>
  );
}
