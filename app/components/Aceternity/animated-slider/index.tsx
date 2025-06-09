/* eslint-disable @typescript-eslint/no-explicit-any */
import { AnimatedTestimonials } from "@/components/ui/animated-slider";

export function AnimatedTestimonialsSlider({
  testimonials,
}: {
  testimonials: any;
}) {
  return <AnimatedTestimonials testimonials={testimonials} />;
}
