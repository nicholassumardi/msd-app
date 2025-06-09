/* eslint-disable @typescript-eslint/no-explicit-any */
"use client";
import { useRef, useState } from "react";
import jsPDF from "jspdf";
import html2canvas from "html2canvas";
import { Hierarchy } from "../../../pages/api/admin/structure";
import "../../../styles/css/custom-hierarchy-second.css";
import { createRoot } from "react-dom/client";

interface TreeNodeProps {
  node: any;
  parentLevel?: number;
}

const TreeNode = ({ node, parentLevel = -1 }: TreeNodeProps) => {
  const hasMultipleChildren = node.children?.length > 1;

  return (
    <div className="flex flex-col items-center relative">
      {parentLevel >= 0 && (
        <div className="absolute w-0.5 bg-gray-300 h-8 -top-8" />
      )}

      <div className="relative">
        <div className="w-48 h-50 bg-white rounded-lg border border-gray-200 p-4 mb-5 shadow-sm">
          <h3 className="text-sm font-semibold text-gray-800 truncate">
            {node.name}
          </h3>
          <p className="text-xs text-gray-500 mt-1 truncate">{node.jobCode}</p>
          <div className="mt-2 space-y-1">
            {node.desc?.map((detail: any, index: number) => (
              <div
                key={index}
                className="text-xs text-gray-600 flex justify-between"
              >
                <span className="truncate">{detail.group}</span>
                <span className="text-gray-500">
                  {detail.position_code_structure}
                </span>
              </div>
            ))}
          </div>
        </div>

        {node.children?.length > 0 && (
          <div className="absolute left-1/2 bottom-0 w-0.5 h-4 bg-gray-300 -translate-x-1/2" />
        )}
      </div>

      {node.children?.length > 0 && (
        <div className="relative">
          {hasMultipleChildren && (
            <div className="absolute inset-x-0 top-0 flex justify-between px-12">
              <div className="w-0.5 h-px bg-gray-300" />
              <div className="w-full h-px bg-gray-300" />
              <div className="w-0.5 h-px bg-gray-300" />
            </div>
          )}

          <div className="flex space-x-16 pt-8 relative">
            {node.children.map((child: any) => (
              <div key={child.id} className="relative">
                <div className="absolute left-1/2 -top-4 w-0.5 h-4 bg-gray-300" />
                <TreeNode node={child} parentLevel={node.level} />
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
};

interface HierarchyChartProps {
  data: Hierarchy;
}

interface PaginationNode extends Hierarchy {
  isContinued?: boolean;
  pageNumber?: number;
  totalPages?: number;
  originalChildren?: any[];
}

const processHierarchy = (node: Hierarchy): PaginationNode[] => {
  const processNode = (currentNode: Hierarchy): PaginationNode[] => {
    if (!currentNode.children || currentNode.children.length === 0)
      return [currentNode as PaginationNode];

    // Process children first
    let processedChildren: PaginationNode[] = [];
    for (const child of currentNode.children) {
      processedChildren = processedChildren.concat(processNode(child));
    }

    // Split current node's children into pages
    const chunks = [];
    for (let i = 0; i < processedChildren.length; i += 6) {
      chunks.push(processedChildren.slice(i, i + 6));
    }

    if (chunks.length <= 1) {
      return [{ ...currentNode, children: processedChildren }];
    }

    // Create paginated nodes
    return chunks.map((chunk, index) => ({
      ...currentNode,
      children: chunk,
      originalChildren: processedChildren,
      isContinued: index < chunks.length - 1,
      pageNumber: index + 1,
      totalPages: chunks.length,
    }));
  };

  return processNode(node);
};

const CustomHierarchyChart = ({ data }: HierarchyChartProps) => {
  const [zoom, setZoom] = useState(0.7);
  const nodes = Array.isArray(data) ? data : [data];
  const chartRef = useRef<HTMLDivElement>(null);
  const containerRef = useRef<HTMLDivElement>(null);

  const handleDownloadPdf = async () => {
    if (!containerRef.current) return;

    const pdf = new jsPDF({
      orientation: "landscape",
      unit: "mm",
      format: "a3",
    });

    // Process all nodes with hierarchical pagination
    const allPages = nodes.flatMap((node) => processHierarchy(node));

    for (let pageIndex = 0; pageIndex < allPages.length; pageIndex++) {
      const pageNode = allPages[pageIndex];

      // Create temporary container
      const tempContainer = document.createElement("div");
      tempContainer.style.position = "fixed";
      tempContainer.style.left = "-10000px";
      tempContainer.style.width = `${containerRef.current.offsetWidth}px`;
      tempContainer.style.backgroundColor = "white";
      document.body.appendChild(tempContainer);

      // Render the page node with React
      const root = createRoot(tempContainer);
      await new Promise<void>((resolve) => {
        root.render(
          <div style={{ transform: "scale(1)", transformOrigin: "top center" }}>
            <TreeNode node={pageNode} />
            {pageNode.isContinued && (
              <div className="mt-4 text-center text-sm text-gray-500 p-4">
                Continued on page {pageNode.pageNumber! + 1} (Page{" "}
                {pageIndex + 2} of {allPages.length})
              </div>
            )}
          </div>
        );
        setTimeout(resolve, 100); // Allow React to render
      });

      // Capture content
      const canvas = await html2canvas(tempContainer, {
        scale: 1.5,
        useCORS: true,
        logging: false,
        width: tempContainer.scrollWidth,
        height: tempContainer.scrollHeight,
      });

      // Add to PDF
      const imgData = canvas.toDataURL("image/jpeg", 0.9);
      const pageWidth = pdf.internal.pageSize.getWidth();
      const pageHeight = pdf.internal.pageSize.getHeight();
      const imgRatio = canvas.width / canvas.height;

      let imgWidth = pageWidth;
      let imgHeight = pageWidth / imgRatio;

      if (imgHeight > pageHeight) {
        imgHeight = pageHeight;
        imgWidth = pageHeight * imgRatio;
      }

      if (pageIndex > 0) pdf.addPage();
      pdf.addImage(
        imgData,
        "JPEG",
        (pageWidth - imgWidth) / 2,
        (pageHeight - imgHeight) / 2,
        imgWidth,
        imgHeight
      );

      // Cleanup
      root.unmount();
      document.body.removeChild(tempContainer);
    }

    pdf.save("organization-chart.pdf");
  };

  return (
    <div className="p-4 bg-gray-50 min-h-screen">
      {/* Keep existing controls and main chart rendering */}
      <div className="mb-2 flex gap-2">
        <button
          onClick={() => setZoom(Math.min(zoom + 0.1, 1.2))}
          className="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          Zoom In
        </button>
        <button
          onClick={() => setZoom(Math.max(zoom - 0.1, 0.4))}
          className="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          Zoom Out
        </button>
        <button
          onClick={handleDownloadPdf}
          className="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700"
        >
          Export PDF
        </button>
      </div>

      <div
        ref={containerRef}
        className="overflow-auto rounded-lg border border-gray-200 bg-white p-4"
      >
        <div
          ref={chartRef}
          className="flex justify-center transition-transform duration-300"
          style={{
            transform: `scale(${zoom})`,
            transformOrigin: "top center",
            minWidth: "fit-content",
            minHeight: "fit-content",
          }}
        >
          <div className="flex flex-col items-center">
            {nodes.map((root) => (
              <TreeNode key={root.id} node={root} />
            ))}
          </div>
        </div>
      </div>
    </div>
  );
};

export default CustomHierarchyChart;
