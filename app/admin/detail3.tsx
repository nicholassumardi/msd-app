/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import React from "react";
import dynamic from "next/dynamic";

const OrgChart = dynamic(() => import("@somkid.sim/react-org-chart-ts"), {
  ssr: false,
});

export interface Person {
  id: number;
  avatar: string;
  department: string;
  name: string;
  title: string;
  totalReports: number;
  color?: string;
}

export interface Node {
  id: number;
  person: Person;
  hasChild: boolean;
  hasParent: boolean;
  children: Node[];
}

const getParent = (id: number): Node[] => {
  switch (id) {
    case 1:
      return [
        {
          id: 1332,
          person: {
            id: 1332,
            name: "Parent A",
            title: "Lead",
            avatar: "/images/pepe-tired.jpg",
            department: "Executive",
            totalReports: 0,
          },
          children: [],
          hasChild: false,
          hasParent: false,
        },
      ];
    default:
      return [];
  }
};

const getChildren = (id: number): Node[] => {
  switch (id) {
    case 1:
    case 2:
      return [
        {
          id: 9,
          person: {
            id: 9,
            name: "Child A",
            title: "Lead",
            avatar: "/images/pepe-tired.jpg",
            department: "Executive",
            totalReports: 0,
          },
          children: [],
          hasChild: false,
          hasParent: true,
        },
        {
          id: 99,
          person: {
            id: 99,
            name: "Child B",
            title: "Manager",
            avatar: "/images/pepe-tired.jpg",
            department: "Operations",
            totalReports: 0,
          },
          children: [],
          hasChild: false,
          hasParent: true,
        },
      ];
    default:
      return [];
  }
};

const minimalValidTree: Node = {
  id: 1,
  person: {
    id: 1,
    name: "Captain Node",
    title: "CEO",
    avatar: "/images/pepe-tired.jpg",
    department: "Executive",
    totalReports: 1,
    color: "#FAF3F2",
  },
  children: [
    {
      id: 2,
      person: {
        id: 2,
        name: "Node 2",
        title: "CTO",
        avatar: "/images/pepe-tired.jpg",
        department: "Tech",
        totalReports: 2,
        color: "#FAF3F2",
      },
      children: [],
      hasChild: false,
      hasParent: true,
    },
    {
      id: 3,
      person: {
        id: 3,
        name: "Node 3",
        title: "CFO",
        avatar: "/images/pepe-tired.jpg",
        department: "Finance",
        totalReports: 0,
        color: "#FAF3F2",
      },
      children: [],
      hasChild: false,
      hasParent: true,
    },
    {
      id: 4,
      person: {
        id: 4,
        name: "Node 4",
        title: "COO",
        avatar: "/images/pepe-tired.jpg",
        department: "Operations",
        totalReports: 1,
        color: "#FAF3F2",
      },
      children: [
        {
          id: 5,
          person: {
            id: 5,
            name: "Node 5",
            title: "Manager",
            avatar: "/images/pepe-tired.jpg",
            department: "Operations",
            totalReports: 0,
          },
          children: [],
          hasChild: false,
          hasParent: true,
        },
      ],
      hasChild: true,
      hasParent: true,
    },
  ],
  hasChild: true,
  hasParent: true,
};

export default function OrgChartComponent() {
  let orgConfig: any = {};

  return (
    <div id="root" style={{ width: "100%", height: "600px" }}>
      <OrgChart
        tree={minimalValidTree}
        loadConfig={() => Promise.resolve(orgConfig)}
        onConfigChange={(cfg) => {
          orgConfig = cfg;
        }}
        loadImage={(node) => Promise.resolve(node.person.avatar)}
        loadParent={(node) => Promise.resolve(getParent(node.id))}
        loadChildren={(node) => Promise.resolve(getChildren(node.id))}
        showDetail={(node) => console.log("Detail:", node)}
      />

      <div style={{ marginTop: 16 }}>
        <button id="zoom-in">+</button>
        <button id="zoom-out">–</button>
        <button id="reset-position">R</button>
      </div>
    </div>
  );
}
