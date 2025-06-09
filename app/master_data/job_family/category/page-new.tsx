/* eslint-disable @typescript-eslint/no-explicit-any */
"use client"

import { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Button, Text, Title } from "@mantine/core";
import { IconChevronLeft, IconChevronRight } from "@tabler/icons-react";

// Sample data for the slider
const slides = [
  {
    id: 1,
    image: "/api/placeholder/600/800",
    title: "Natural Landscapes",
    description:
      "Explore breathtaking views of mountains, forests, and lakes. Our collection showcases the beauty of untouched nature from around the world.",
  },
  {
    id: 2,
    image: "/api/placeholder/600/800",
    title: "Urban Architecture",
    description:
      "Discover stunning cityscapes and iconic buildings designed by world-renowned architects. Each structure tells a story of innovation and creativity.",
  },
  {
    id: 3,
    image: "/api/placeholder/600/800",
    title: "Abstract Expressions",
    description:
      "Immerse yourself in the world of abstract art where emotions and concepts are portrayed through color, form, and texture rather than realistic imagery.",
  },
];

export default function Slider() {
  const [currentIndex, setCurrentIndex] = useState(0);
  const [direction, setDirection] = useState(0);

  const goToPrevious = () => {
    setDirection(-1);
    setCurrentIndex((prevIndex) =>
      prevIndex === 0 ? slides.length - 1 : prevIndex - 1
    );
  };

  const goToNext = () => {
    setDirection(1);
    setCurrentIndex((prevIndex) =>
      prevIndex === slides.length - 1 ? 0 : prevIndex + 1
    );
  };

  // Auto-slide functionality
  useEffect(() => {
    const interval = setInterval(() => {
      goToNext();
    }, 5000);

    return () => clearInterval(interval);
  }, [currentIndex]);

  const slideVariants = {
    enter: (direction: any) => ({
      x: direction > 0 ? 1000 : -1000,
      opacity: 0,
    }),
    center: {
      x: 0,
      opacity: 1,
    },
    exit: (direction: any) => ({
      x: direction < 0 ? 1000 : -1000,
      opacity: 0,
    }),
  };

  return (
    <div className="w-full h-screen flex flex-col items-center justify-center bg-gray-100">
      <div className="w-full lg:w-3/4 h-3/4 relative overflow-hidden rounded-lg shadow-xl">
        <AnimatePresence initial={false} custom={direction} mode="wait">
          <motion.div
            key={currentIndex}
            custom={direction}
            variants={slideVariants}
            initial="enter"
            animate="center"
            exit="exit"
            transition={{
              x: { type: "spring", stiffness: 300, damping: 30 },
              opacity: { duration: 0.4 },
            }}
            className="absolute w-full h-full flex flex-col lg:flex-row"
          >
            {/* Image section */}
            <div className="w-full lg:w-1/2 h-1/2 lg:h-full relative overflow-hidden">
              <img
                src={slides[currentIndex].image}
                alt={slides[currentIndex].title}
                className="w-full h-full object-cover"
              />
              <div className="absolute inset-0 bg-gradient-to-b from-transparent to-black/30" />
            </div>

            {/* Description section */}
            <div className="w-full lg:w-1/2 h-1/2 lg:h-full flex flex-col justify-center p-6 lg:p-10 bg-white">
              <motion.div
                initial={{ y: 20, opacity: 0 }}
                animate={{ y: 0, opacity: 1 }}
                transition={{ delay: 0.2, duration: 0.5 }}
              >
                <Title
                  order={2}
                  className="text-2xl lg:text-4xl font-bold mb-4 text-gray-800"
                >
                  {slides[currentIndex].title}
                </Title>
                <Text className="text-lg text-gray-600 mb-8">
                  {slides[currentIndex].description}
                </Text>
                <div className="flex">
                  <Button
                    variant="outline"
                    className="mr-4 border border-gray-300 hover:bg-gray-100"
                  >
                    Learn More
                  </Button>
                  <Button
                    variant="filled"
                    className="bg-blue-600 hover:bg-blue-700"
                  >
                    View Gallery
                  </Button>
                </div>
              </motion.div>
            </div>
          </motion.div>
        </AnimatePresence>

        {/* Navigation buttons */}
        <div className="absolute bottom-4 right-4 flex space-x-2 z-10">
          <button
            onClick={goToPrevious}
            className="p-2 rounded-full bg-white/80 hover:bg-white shadow-md text-gray-800 flex items-center justify-center"
            aria-label="Previous slide"
          >
            <IconChevronLeft size={24} />
          </button>
          <button
            onClick={goToNext}
            className="p-2 rounded-full bg-white/80 hover:bg-white shadow-md text-gray-800 flex items-center justify-center"
            aria-label="Next slide"
          >
            <IconChevronRight size={24} />
          </button>
        </div>

        {/* Slide indicators */}
        <div className="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-10">
          {slides.map((_, index) => (
            <button
              key={index}
              onClick={() => {
                setDirection(index > currentIndex ? 1 : -1);
                setCurrentIndex(index);
              }}
              className={`w-2 h-2 rounded-full ${
                index === currentIndex ? "bg-blue-600 w-6" : "bg-white/60"
              } transition-all duration-300`}
              aria-label={`Go to slide ${index + 1}`}
            />
          ))}
        </div>
      </div>
    </div>
  );
}
