import { NavbarDemo } from "./components/Aceternity/navbar";
import { TypewriterEffectResult } from "./components/Aceternity/typewriter-effect";
import { Vortex } from "./components/ui/vortex";

export default function Home() {
  return (
    <>
      <head>
        <title>Home</title>
        <link rel="icon" href="/images/images.jpeg" />
      </head>
      <div className="w-[calc(100%)] h-screen overflow-hidden">
        <Vortex
          backgroundColor="black"
          rangeY={800}
          particleCount={500}
          baseHue={120}
          className="flex items-center flex-col justify-center px-2 md:px-10  py-4 w-full h-full"
        >
          <h2 className="bg-clip-text text-transparent text-start bg-gradient-to-b dark:from-neutral-900 dark:to-neutral-700 from-white to-stone-200  text-2xl md:text-4xl lg:text-9xl font-sans py-2 md:py-2 relative z-20 font-bold tracking-tight opacity-90">
            MSD
          </h2>
          <TypewriterEffectResult />
          <NavbarDemo />
        </Vortex>
      </div>
    </>
  );
}
