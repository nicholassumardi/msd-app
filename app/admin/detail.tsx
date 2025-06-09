/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";

import React, { useEffect, useState } from "react";
import ChartContainer from "@dormammuuuuu/nextjs-orgchart";
import "@dormammuuuuu/nextjs-orgchart/ChartContainer.css";
import "@dormammuuuuu/nextjs-orgchart/ChartNode.css";
import "../../styles/css/custom-hierarchy.css";

// 1) Original, fully-populated tree
const rawData = {
  id: "1",
  name: "CEO",
  relationship: "00",
  children: [
    {
      id: "2",
      name: "CTO",
      relationship: "01",
      children: [
        { id: "3", name: "Dev Lead", relationship: "02" },
        { id: "4", name: "QA Lead", relationship: "02" },
      ],
    },
    {
      id: "5",
      name: "CFO",
      relationship: "01",
      children: [{ id: "6", name: "Accounting Lead", relationship: "02" }],
    },
  ],
};

// 2) Helper: clone & inject `collapsed` flags everywhere
function annotateCollapse(node: any, pathToOpen: Set<string>): any {
  // mark collapsed unless this node is on the path-to-open
  const isOnOpenPath = pathToOpen.has(node.id);
  return {
    ...node,
    // collapsed = true means “hide its children”
    collapsed: !isOnOpenPath,
    children: node.children?.map((c: any) => annotateCollapse(c, pathToOpen)),
  };
}

// 3) Find the path (list of ids) from root → targetName
function buildOpenPath(
  node: any,
  targetName: string,
  acc: string[] = []
): string[] | null {
  const newAcc = [...acc, node.id];
  if (node.name === targetName) return newAcc;
  if (node.children) {
    for (const child of node.children) {
      const sub = buildOpenPath(child, targetName, newAcc);
      if (sub) return sub;
    }
  }
  return null;
}

// 4) Custom node template (same as yours)
const MyNodeTemplate = ({ nodeData }: any) => {
  /* …your getNodeStyles + JSX here… */
  // (omitted for brevity—just copy in your existing template)
  return <div>…</div>;
};

export default function App() {
  const [treeData, setTreeData] = useState<any>(null);
  const targetName = "CTO";

  useEffect(() => {
    // compute path of IDs: ["1", "2"] for CEO→CTO
    const path = buildOpenPath(rawData, targetName);
    if (!path) {
      console.warn(`Couldn’t find node named "${targetName}"`);
      setTreeData(rawData);
      return;
    }

    // turn that into a Set for quick lookups
    const pathSet = new Set(path);

    // annotate the entire tree: only nodes on pathSet stay un‑collapsed
    const annotated = annotateCollapse(rawData, pathSet);
    setTreeData(annotated);
  }, []);

  if (!treeData) return null; // or a spinner

  return (
    <ChartContainer
      datasource={treeData}
      pan
      zoom
      NodeTemplate={MyNodeTemplate}
      draggable={false}
      // collapsible is true by default
      onClickNode={(node: any) => console.log("Node clicked:", node)}
    />
  );
}
