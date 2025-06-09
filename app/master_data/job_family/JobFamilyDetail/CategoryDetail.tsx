/* eslint-disable @typescript-eslint/no-explicit-any */
import React, { useEffect, useState } from "react";
import { Paper, Avatar } from "@mantine/core";
import { motion, AnimatePresence } from "framer-motion";
import { Category } from "../../../../pages/api/admin/master_data/job_family/category";

interface CategoryProps {
  category: Category | null;
}

const CategoryDetail: React.FC<CategoryProps> = ({ category }) => {
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
    return () => setMounted(false);
  }, []);

  // Map level (0-10) to circle size
  const getCircleSize = () => {
    const max = 40;
    return `${max}px`;
  };

  // Animation variants
  const containerVariants = {
    hidden: { opacity: 0 },
    show: {
      opacity: 1,
      transition: { staggerChildren: 0.05, delayChildren: 0.2 },
    },
  };

  const itemVariants = {
    hidden: { y: 20, opacity: 0 },
    show: {
      y: 0,
      opacity: 1,
      transition: { type: "spring", stiffness: 200, damping: 20 },
    },
  };

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.6, ease: "easeOut" }}
      className="h-full w-full"
    >
      <Paper
        shadow="xl"
        radius="lg"
        className="h-full flex flex-col bg-white border border-gray-200 overflow-hidden font-satoshi"
      >
        {/* Header */}
        <motion.div
          className="bg-violet-400 p-8 border-b border-gray-200"
          initial={{ height: "25%" }}
          animate={{ height: "auto" }}
          transition={{ duration: 0.5 }}
        >
          <div className="flex justify-between items-center">
            <div>
              <motion.h3
                layoutId={`title-${category?.name}`}
                className="text-3xl font-bold text-gray-900"
              >
                {category?.name || "Category"}
              </motion.h3>
              <div className="mt-2 inline-flex items-center bg-violet-100 rounded-full px-4 py-2 border border-violet-400">
                <span className="text-gray-900 text-lg font-semibold mr-1">
                  {category?.job_code?.length || 0}
                </span>
                <span className="text-gray-600 text-sm">
                  Job Position{category?.job_code?.length !== 1 ? "s" : ""}
                </span>
              </div>
            </div>
            <Avatar
              size={64}
              radius={64}
              className="bg-white text-gray-900 border border-gray-300"
            >
              <span className="text-2xl font-bold text-gray-900">
                {category?.name?.charAt(0) || "?"}
              </span>
            </Avatar>
          </div>
        </motion.div>

        {/* Table */}
        <div className="flex-grow overflow-auto bg-white">
          <AnimatePresence>
            <motion.div
              variants={containerVariants}
              initial="hidden"
              animate="show"
              className="px-6 py-4 space-y-3"
            >
              {/* Header Row */}
              <div className="grid grid-cols-4 gap-5 mb-2 px-4">
                {["Full Code", "Position", "Org Level", "Level"].map(
                  (header) => (
                    <div
                      key={header}
                      className="text-xs uppercase text-gray-500 font-semibold"
                    >
                      {header}
                    </div>
                  )
                )}
              </div>

              {/* Data Rows */}
              {category?.job_code?.map((job) => (
                <motion.div
                  key={job.id}
                  variants={itemVariants}
                  className="grid grid-cols-4 gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200 transition-colors duration-300"
                  whileHover={{
                    borderColor: "#60A5FA",
                    backgroundColor: "#F0F9FF",
                  }}
                >
                  <div className="text-gray-900 font-mono text-sm">
                    {job.full_code}
                  </div>
                  <div className="text-gray-900 text-sm">{job.position}</div>
                  <div className="text-gray-900 text-sm">{job.org_level}</div>
                  <div className=" items-center">
                    <div
                      className="relative rounded-full border-2 border-gray-300 flex items-center justify-center"
                      style={{
                        width: getCircleSize(),
                        height: getCircleSize(),
                      }}
                    >
                      <span className="absolute inset-0 flex items-center justify-center text-gray-900 font-bold">
                        {job.level}
                      </span>
                      <div className="absolute inset-0 rounded-full bg-white-50/30" />
                    </div>
                  </div>
                </motion.div>
              ))}

              {/* Empty State */}
              {!category?.job_code?.length && mounted && (
                <div className="col-span-4 flex flex-col items-center justify-center py-16 text-gray-400">
                  <div className="relative w-24 h-24 mb-6 animate-spin-slow border-2 border-dashed border-gray-200 rounded-lg" />
                  <p className="text-lg font-medium text-gray-900 mb-2">
                    No job positions yet
                  </p>
                  <p className="text-sm text-gray-500 text-center max-w-xs">
                    Job positions will appear here once they&apos;re added to
                    this category
                  </p>
                </div>
              )}
            </motion.div>
          </AnimatePresence>
        </div>
      </Paper>
    </motion.div>
  );
};

export default CategoryDetail;
