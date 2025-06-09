/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import { useRef } from "react";
import dynamic from "next/dynamic";
import "@styles/css/org-chart.css";

const OrgChart = dynamic(() => import("@unicef/react-org-chart"), {
  ssr: false,
});

export interface Person {
  id: number;
  avatar: string;
  department: string;
  name: string;
  title: string;
  totalReports: number;
}

export interface Node {
  id: number;
  person: Person;
  hasChild: boolean;
  hasParent: boolean;
  isHighlight?: boolean;
  children: Node[];
  parentId?: number;
}

export default function UNICEFOrgChart() {
  const configRef = useRef<any>({});

  // Root node
  const tree: Node = {
    id: 100,
    person: {
      id: 100,
      avatar: "images/pepe-tired.jpg",
      department: "",
      name: "Henry monger",
      title: "Manager",
      totalReports: 3,
    },
    hasChild: true,
    hasParent: true,
    children: [],
    parentId: 1,
  };

  // Lazy-load children based on node ID
  const getChild = (id: number): Node[] => {
    switch (id) {
      case 100:
        return tree1;
      case 36:
        return tree2;
      case 56:
        return tree3;
      case 25:
        return tree4;
      default:
        console.warn(`No children for node ${id}`);
        return [];
    }
  };

  // Lazy-load a parent for "go up"
  const getParent = (d: Node): Node | undefined => {
    if (d.id === 100) {
      return {
        id: 500,
        person: {
          id: 500,
          avatar: "images/pepe-tired.jpg",
          department: "",
          name: "Pascal ruth",
          title: "Member",
          totalReports: 1,
        },
        hasChild: false,
        hasParent: true,
        children: [d],
      };
    } else if (d.id === 500) {
      return {
        id: 1,
        person: {
          id: 1,
          avatar: "images/pepe-tired.jpg",
          department: "",
          name: "Bryce joe",
          title: "Director",
          totalReports: 1,
        },
        hasChild: false,
        hasParent: false,
        children: [d],
      };
    }
    return undefined;
  };

  // Sample child-node arrays
  const tree1: Node[] = [
    {
      id: 36,
      person: {
        id: 36,
        avatar: "images/pepe-tired.jpg",
        department: "",
        name: "Tomasz polaski",
        title: "IT Specialist",
        totalReports: 4,
      },
      hasChild: true,
      hasParent: true,
      children: [],
    },
    {
      id: 32,
      person: {
        id: 32,
        avatar: "images/pepe-tired.jpg",
        department: "",
        name: "Emanuel walker",
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
        avatar: "images/pepe-tired.jpg",
        department: "",
        name: "Kerry peter",
        title: "IT Specialist",
        totalReports: 3,
      },
      hasChild: true,
      hasParent: true,
      children: [],
    },
  ];

  const tree2: Node[] = [
    {
      id: 56,
      person: {
        id: 56,
        avatar: "images/pepe-tired.jpg",
        department: "",
        name: "Sam John",
        title: "HR",
        totalReports: 2,
      },
      hasChild: true,
      hasParent: true,
      children: [],
    },
    {
      id: 66,
      person: {
        id: 66,
        avatar: "images/pepe-tired.jpg",
        department: "",
        name: "John doe",
        title: "Developer",
        totalReports: 0,
      },
      hasChild: false,
      hasParent: true,
      children: [],
    },
  ];

  const tree3: Node[] = [
    {
      id: 70,
      person: {
        id: 70,
        avatar: "images/pepe-tired.jpg",
        department: "",
        name: "Kenneth dom",
        title: "IT Officer",
        totalReports: 0,
      },
      hasChild: false,
      hasParent: true,
      children: [],
    },
  ];

  const tree4: Node[] = [
    {
      id: 102,
      person: {
        id: 102,
        avatar: "images/pepe-tired.jpg",
        department: "",
        name: "Hendy kinger",
        title: "Manager",
        totalReports: 0,
      },
      hasChild: false,
      hasParent: true,
      children: [],
    },
  ];

  return (
    <div id="root">
      <OrgChart
        tree={tree}
        downloadImageId="download-image"
        downloadPdfId="download-pdf"
        onConfigChange={(newConfig) => {
          configRef.current = newConfig;
        }}
        loadConfig={() => configRef.current}
        loadImage={(p) => Promise.resolve(p.person.avatar)}
        loadChildren={(node) => Promise.resolve(getChild(node.id))}
        loadParent={(node) => Promise.resolve(getParent(node))}
      />
    </div>
  );
}
