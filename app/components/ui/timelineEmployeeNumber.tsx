"use client";
import { useScroll, useTransform, motion } from "framer-motion";
import React, { useEffect, useRef, useState } from "react";

interface TimelineEntry {
  title: string;
  content: React.ReactNode;
  date?: string;
}

export const Timeline = ({ data }: { data: TimelineEntry[] }) => {
  const ref = useRef<HTMLDivElement>(null);
  const containerRef = useRef<HTMLDivElement>(null);
  const [height, setHeight] = useState(0);

  useEffect(() => {
    if (ref.current) {
      const rect = ref.current.getBoundingClientRect();
      setHeight(rect.height);
    }
  }, [ref, data]);

  const { scrollYProgress } = useScroll({
    target: containerRef,
    offset: ["start 10%", "end 50%"],
  });

  const heightTransform = useTransform(scrollYProgress, [0, 1], [0, height]);
  const opacityTransform = useTransform(scrollYProgress, [0, 0.1], [0, 1]);

  return (
    <div
      className="w-full bg-white dark:bg-neutral-950 font-sans md:px-10 font-medium"
      ref={containerRef}
    >
      <div ref={ref} className="relative max-w-15xl mx-auto pb-20">
        {data.map((item, index) => (
          <div
            key={index}
            className="flex justify-start pt-10 md:pt-20 md:gap-10 group"
          >
            {/* Timeline Marker */}
            <div className="sticky flex flex-col md:flex-row z-40 items-center top-40 self-start max-w-xs lg:max-w-sm md:w-full">
              <div className="h-10 absolute left-3 md:left-3 w-10 rounded-full bg-white dark:bg-black flex items-center justify-center">
                <motion.div
                  initial={{ scale: 0 }}
                  animate={{ scale: 1 }}
                  transition={{
                    type: "spring",
                    stiffness: 300,
                    damping: 10,
                    delay: index * 0.2,
                  }}
                  className="h-4 w-4 rounded-full bg-blue-500 group-hover:bg-blue-600 transition-colors"
                />
              </div>
              <h3 className="hidden md:block text-xl md:pl-20 md:text-3xl font-bold text-neutral-400 dark:text-neutral-500 group-hover:text-neutral-600 dark:group-hover:text-neutral-300 transition-colors">
                {item.title}
              </h3>
            </div>

            {/* Content */}
            <motion.div
              initial={{ opacity: 0, x: -50 }}
              whileInView={{ opacity: 1, x: 0 }}
              transition={{
                type: "spring",
                stiffness: 100,
                damping: 10,
                delay: index * 0.2,
              }}
              className="relative pl-20 pr-4 md:pl-4 w-full"
            >
              <h3 className="md:hidden block text-2xl mb-4 text-left font-bold text-neutral-400 dark:text-neutral-500">
                {item.title}
              </h3>
              {item.date && (
                <p className="text-sm text-neutral-500 dark:text-neutral-400 mb-2">
                  {item.date}
                </p>
              )}
              {item.content}
            </motion.div>
          </div>
        ))}

        {/* Animated Timeline Line */}
        <div
          style={{
            height: height + "px",
          }}
          className="absolute md:left-8 left-8 top-0 overflow-hidden w-[2px] bg-[linear-gradient(to_bottom,var(--tw-gradient-stops))] from-transparent from-[0%] via-neutral-200 dark:via-neutral-700 to-transparent to-[99%] [mask-image:linear-gradient(to_bottom,transparent_0%,black_10%,black_90%,transparent_100%)]"
        >
          <motion.div
            style={{
              height: heightTransform,
              opacity: opacityTransform,
            }}
            className="absolute inset-x-0 top-0 w-[2px] bg-gradient-to-t from-blue-500 via-blue-500 to-transparent from-[0%] via-[10%] rounded-full"
          />
        </div>
      </div>
    </div>
  );
};
