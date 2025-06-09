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
  parentId?: number;
  person: Person;
  hasChild: boolean;
  hasParent: boolean;
  isHighlight?: boolean;
  children: Node[];
}

export default function EmployeeOrgChart({
  tree,
  getChild,
  getParent,
}: {
  tree: Node;
  getChild: (id: number) => Promise<Node[]>;
  getParent: (node: Node) => Promise<Node | undefined>;
}) {
  const configRef = useRef<any>({});

  return (
    <div id="root" className="z-99999">
      {tree && (
        <OrgChart
          tree={tree} // Pass tree directly
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
      )}
    </div>
  );
}
