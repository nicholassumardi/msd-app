/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import React, { useState } from "react";
import dynamic from "next/dynamic";

const OrgChart = dynamic(() => import("@unicef/react-org-chart"), {
  ssr: false,
});

interface Person {
  id: number;
  avatar: string;
  department: string;
  name: string;
  title: string;
  totalReports: number;
}

interface Node {
  id: number;
  person: Person;
  hasChild: boolean;
  hasParent: boolean;
  children: Node[];
}

const getChildren = (id: number): Node[] => {
  switch (id) {
    case 100:
    case 1:
    case 2:
      return [
        {
          id: 36,
          person: {
            id: 36,
            avatar: "/images/pepe-tired.jpg",
            department: "IT",
            name: "Tomasz Polaski",
            title: "IT Specialist",
            totalReports: 4,
          },
          hasChild: false,
          hasParent: true,
          children: [],
        },
        {
          id: 32,
          person: {
            id: 32,
            avatar: "/images/pepe-tired.jpg",
            department: "IT",
            name: "Emanuel Walker",
            title: "IT Specialist",
            totalReports: 0,
          },
          hasChild: false,
          hasParent: true,
          children: [],
        },
        {
          id: 25,
          person: {
            id: 25,
            avatar: "/images/pepe-tired.jpg",
            department: "IT",
            name: "Kerry Peter",
            title: "IT Specialist",
            totalReports: 3,
          },
          hasChild: false,
          hasParent: true,
          children: [],
        },
      ];
    default:
      return [];
  }
};

// 2. Preload root’s children
const rootNode: Node = {
  id: 100,
  person: {
    id: 100,
    avatar: "/images/pepe-tired.jpg",
    department: "Management",
    name: "Henry Monger",
    title: "Manager",
    totalReports: 3,
  },
  hasChild: true,
  hasParent: false,
  children: getChildren(100),
};

export default function OrgChartComponent() {
  const [config, setConfig] = useState<any>(null);

  return (
    <div id="root" style={{ width: "100%", height: "600px" }}>
      <OrgChart
        tree={rootNode}
        downloadImageId="download-image"
        downloadPdfId="download-pdf"
        onConfigChange={(c) => {
          if (c) setConfig(c);
        }}
        loadConfig={() => config}
        loadChildren={(node) =>
          node.hasChild
            ? Promise.resolve(getChildren(node.id))
            : Promise.resolve([])
        }
        loadImage={(node) =>
          Promise.resolve(node.person.avatar || "/images/default-avatar.png")
        }
      />

      {/* Optional controls */}
      <div style={{ marginTop: 16 }}>
        <button onClick={() => config?.zoomIn()} style={{ marginRight: 8 }}>
          Zoom In
        </button>
        <button onClick={() => config?.zoomOut()} style={{ marginRight: 8 }}>
          Zoom Out
        </button>
        <button onClick={() => config?.resetPosition()}>Reset</button>
      </div>
    </div>
  );
}
