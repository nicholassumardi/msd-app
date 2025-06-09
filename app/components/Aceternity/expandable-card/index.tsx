"use client";
import Image from "next/image";
import React, { useEffect, useId, useRef, useState } from "react";
import { AnimatePresence, motion } from "framer-motion";
import { useOutsideClick } from "../../../../hooks/useOutsideClick";
import { Category } from "../../../../pages/api/admin/master_data/job_family/category";

export function ExpandableJobCategoryCard({ data }: { data: Category[] }) {
  const [active, setActive] = useState<Category | null>(null);
  const id = useId();
  const ref = useRef<HTMLDivElement>(null);

  useEffect(() => {
    function onKeyDown(event: KeyboardEvent) {
      if (event.key === "Escape") {
        setActive(null);
      }
    }

    if (active) {
      document.body.style.overflow = "hidden";
    } else {
      document.body.style.overflow = "auto";
    }

    window.addEventListener("keydown", onKeyDown);
    return () => window.removeEventListener("keydown", onKeyDown);
  }, [active]);

  useOutsideClick(ref, () => setActive(null));

  return (
    <>
      <AnimatePresence>
        {active && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 bg-black/20 h-full w-full z-10"
          />
        )}
      </AnimatePresence>
      <AnimatePresence>
        {active ? (
          <div className="fixed inset-0 grid place-items-center z-[100]">
            <motion.button
              key={`button-${active.name}-${id}`}
              layout
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{
                opacity: 0,
                transition: {
                  duration: 0.05,
                },
              }}
              className="flex absolute top-2 right-2 lg:hidden items-center justify-center bg-white rounded-full h-6 w-6"
              onClick={() => setActive(null)}
            >
              <CloseIcon />
            </motion.button>
            <motion.div
              layoutId={`card-${active.name}-${id}`}
              ref={ref}
              className="w-full max-w-[500px] h-full md:h-fit md:max-h-[90%] flex flex-col bg-white dark:bg-neutral-900 sm:rounded-3xl overflow-hidden"
            >
              <motion.div layoutId={`image-${active.name}-${id}`}>
                <Image
                  priority
                  width={200}
                  height={200}
                  src="/images/pepe-tired.jpg"
                  alt={active.name}
                  className="w-full h-80 lg:h-80 sm:rounded-tr-lg sm:rounded-tl-lg object-cover object-top"
                />
              </motion.div>

              <div className="flex flex-col overflow-hidden">
                <div className="flex justify-between items-start p-4">
                  <div className="">
                    <motion.h3
                      layoutId={`title-${active.name}-${id}`}
                      className="font-medium text-neutral-700 dark:text-neutral-200 text-base"
                    >
                      {active.name}
                    </motion.h3>
                    <motion.p className="text-neutral-600 dark:text-neutral-400 text-base">
                      {active.job_code?.length} Job Codes
                    </motion.p>
                  </div>
                </div>
                <div className="flex-grow overflow-auto relative px-4 pb-4">
                  <div className="max-h-full overflow-y-auto">
                    <table className="w-full border-collapse">
                      <thead className="sticky top-0 bg-white dark:bg-neutral-900 z-10">
                        <tr className="bg-neutral-100 dark:bg-neutral-800">
                          <th className="p-2 text-left">Full Code</th>
                          <th className="p-2 text-left">Position</th>
                          <th className="p-2 text-left">Org Level</th>
                          <th className="p-2 text-left">Level</th>
                        </tr>
                      </thead>
                      <tbody>
                        {active.job_code &&
                          active.job_code.map((job) => (
                            <tr
                              key={job.id}
                              className="border-b dark:border-neutral-700"
                            >
                              <td className="p-2">{job.full_code}</td>
                              <td className="p-2">{job.position}</td>
                              <td className="p-2">{job.org_level}</td>
                              <td className="p-2">{job.level}</td>
                            </tr>
                          ))}
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </motion.div>
          </div>
        ) : null}
      </AnimatePresence>
      <ul className="max-w-7xl mx-auto w-full grid grid-cols-2 md:grid-cols-4 items-start gap-4">
        {data &&
          data.map((category) => (
            <motion.div
              layoutId={`card-${category.name}-${id}`}
              key={category.name}
              onClick={() => setActive(category)}
              className="p-4 flex flex-col hover:bg-neutral-50 dark:hover:bg-neutral-800 rounded-xl cursor-pointer"
            >
              <div className="flex gap-4 flex-col w-full">
                <motion.div layoutId={`image-${category.name}-${id}`}>
                  <Image
                    width={100}
                    height={100}
                    src="/images/pepe-tired.jpg"
                    alt={category.name}
                    className="h-60 w-full rounded-lg object-cover object-top"
                  />
                </motion.div>
                <div className="flex justify-center items-center flex-col">
                  <motion.h3
                    layoutId={`title-${category.name}-${id}`}
                    className="font-medium text-neutral-800 dark:text-neutral-200 text-center md:text-left text-base"
                  >
                    {category.name}
                  </motion.h3>
                  <motion.p className="text-neutral-600 dark:text-neutral-400 text-center md:text-left text-base">
                    {category.job_code.length} Job Codes
                  </motion.p>
                </div>
              </div>
            </motion.div>
          ))}
      </ul>
    </>
  );
}

export const CloseIcon = () => {
  return (
    <motion.svg
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      exit={{
        opacity: 0,
        transition: {
          duration: 0.05,
        },
      }}
      xmlns="http://www.w3.org/2000/svg"
      width="24"
      height="24"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
      className="h-4 w-4 text-black"
    >
      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
      <path d="M18 6l-12 12" />
      <path d="M6 6l12 12" />
    </motion.svg>
  );
};
