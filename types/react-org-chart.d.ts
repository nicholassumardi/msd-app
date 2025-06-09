/* eslint-disable @typescript-eslint/no-explicit-any */
declare module "@unicef/react-org-chart" {
  import { FC } from "react";

  export interface Person {
    id: number;
    avatar: string;
    department: string;
    name: string;
    title: string;
    totalReports: number;
  }

  export interface TreeNode {
    id: number;
    person: Person;
    hasChild: boolean;
    hasParent: boolean;
    isHighlight?: boolean;
    children: TreeNode[];
  }

  export interface OrgChartProps {
    tree: TreeNode;
    downloadImageId?: string;
    downloadPdfId?: string;
    onConfigChange: (config: any) => void;
    loadConfig: (d: any) => any;
    loadParent: (personData: any) => Promise<any>;
    loadChildren: (personData: any) => Promise<any>;
    loadImage?: (personData: any) => Promise<any>;
  }

  const OrgChart: FC<OrgChartProps>;
  export default OrgChart;
}
