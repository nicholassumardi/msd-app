"use client";

import { TypewriterEffect } from "@/components/ui/typewriter-effect";

export function TypewriterEffectResult() {
  const words = [
    {
      text: "Management",
    },
    {
      text: "System",
    },
    {
      text: "Development",
      className: "text-meta-3 dark:text-blue-400",
    },
  ];

  return <TypewriterEffect words={words} />;
}
