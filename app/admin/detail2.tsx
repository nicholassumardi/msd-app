"use client";

import React, { useState, useEffect, useRef } from "react";
import dynamic from "next/dynamic";

// Dynamically import Tree to prevent SSR issues
const Tree = dynamic(() => import("react-d3-tree"), { ssr: false });

// Define the structure of each node
interface TreeNode {
  name: string;
  attributes?: {
    title?: string;
    department?: string;
  };
  children?: TreeNode[];
}

const OrgChart: React.FC = () => {
  const [translate, setTranslate] = useState<{ x: number; y: number }>({
    x: 0,
    y: 0,
  });
  const treeContainer = useRef<HTMLDivElement>(null);

  // Sample organizational data
  const treeData: TreeNode[] = [
    {
      name: "Jane Doe",
      attributes: {
        title: "CEO",
        department: "Executive",
      },
      children: [
        {
          name: "John Smith",
          attributes: {
            title: "CTO",
            department: "Technology",
          },
          children: [
            {
              name: "Alice Johnson",
              attributes: {
                title: "Lead Developer",
                department: "Software",
              },
            },
            {
              name: "Bob Brown",
              attributes: {
                title: "QA Manager",
                department: "Quality Assurance",
              },
            },
          ],
        },
        {
          name: "Emily Davis",
          attributes: {
            title: "CFO",
            department: "Finance",
          },
        },
      ],
    },
  ];

  // Center the tree on mount
  useEffect(() => {
    if (treeContainer.current) {
      const dimensions = treeContainer.current.getBoundingClientRect();
      setTranslate({
        x: dimensions.width / 2,
        y: dimensions.height / 4,
      });
    }
  }, []);

  return (
    <div style={{ width: "100%", height: "600px" }} ref={treeContainer}>
      <Tree
        data={treeData}
        translate={translate}
        orientation="vertical"
        pathFunc="elbow"
        collapsible={true}
        zoomable={true}
        separation={{ siblings: 1, nonSiblings: 2 }}
        nodeSize={{ x: 200, y: 100 }}
      />
    </div>
  );
};

export default OrgChart;
