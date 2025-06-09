"use client";

import "@styles/css/satoshi.css";
import "@styles/css/style.css";
import "@mantine/charts/styles.css";
import "@mantine/dropzone/styles.css";
import "./globals.css";
import React, { useEffect, useState } from "react";
import Loader from "@/components/common/Loader";
import { MantineProvider } from "@mantine/core";
import { Notifications } from "@mantine/notifications";

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const [loading, setLoading] = useState<boolean>(true);

  // const pathname = usePathname();

  useEffect(() => {
    setTimeout(() => setLoading(false), 500);
  }, []);

  return (
    <html lang="en">
      <body suppressHydrationWarning={true}>
        <div className="dark:bg-boxdark-2 dark:text-bodydark">
          <MantineProvider
            theme={{
              primaryColor: "violet",
            }}
          >
            <Notifications />
            {loading ? <Loader /> : children}
          </MantineProvider>
        </div>
      </body>
    </html>
  );
}
