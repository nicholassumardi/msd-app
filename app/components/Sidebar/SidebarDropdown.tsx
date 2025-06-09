/* eslint-disable @typescript-eslint/no-explicit-any */
import React from "react";
import Link from "next/link";
import { usePathname } from "next/navigation";
import SidebarDropdownChildren from "./SidebarDropdownChild";

const SidebarDropdown = ({
  item,
  childrenPageName,
  setChildrenPageName,
}: any) => {
  const handleClick = () => {
    const updatedChildrenPageName =
      childrenPageName !== item.label.toLowerCase()
        ? item.label.toLowerCase()
        : "";
    setChildrenPageName(updatedChildrenPageName);
  };

  const pathname = usePathname();

  return (
    <>
      <ul className="mb-5.5 mt-4 flex flex-col gap-2.5 pl-6">
        <li>
          <Link
            href={item.route}
            scroll={false}
            onClick={handleClick}
            className={`group relative flex items-center gap-2.5 rounded-md px-4 font-medium text-bodydark2 duration-300 ease-in-out hover:text-white ${
              pathname === item.route ? "text-white" : ""
            }`}
          >
            {item.label}
            {item.grandchildren && (
              <svg
                className={`absolute right-4 top-1/2 -translate-y-1/2 fill-current ${
                  childrenPageName === item.label.toLowerCase() && "rotate-180"
                }`}
                width="20"
                height="20"
                viewBox="0 0 20 20"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  fillRule="evenodd"
                  clipRule="evenodd"
                  d="M4.41107 6.9107C4.73651 6.58527 5.26414 6.58527 5.58958 6.9107L10.0003 11.3214L14.4111 6.91071C14.7365 6.58527 15.2641 6.58527 15.5896 6.91071C15.915 7.23614 15.915 7.76378 15.5896 8.08922L10.5896 13.0892C10.2641 13.4147 9.73651 13.4147 9.41107 13.0892L4.41107 8.08922C4.08563 7.76378 4.08563 7.23614 4.41107 6.9107Z"
                  fill=""
                />
              </svg>
            )}
          </Link>
          {item.grandchildren && (
            <div
              className={`translate transform overflow-hidden ${
                childrenPageName !== item.label.toLowerCase() && "hidden"
              }`}
            >
              {item.grandchildren.map((item: any, index: number) => (
                <SidebarDropdownChildren key={index} item={item} />
              ))}
            </div>
          )}
        </li>
      </ul>
    </>
  );
};

export default SidebarDropdown;
