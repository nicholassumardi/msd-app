/* eslint-disable @typescript-eslint/no-explicit-any */

import React, { useEffect, useState } from "react";
import {
  Group,
  Select,
  Button,
  Card,
  Title,
  Text,
  Divider,
  ActionIcon,
  Tooltip,
  Badge,
  Transition,
} from "@mantine/core";
import {
  IconSearch,
  IconFilter,
  IconUser,
  IconX,
  IconRefresh,
} from "@tabler/icons-react";
import { Category } from "../../../../pages/api/admin/master_data/job_family/category";
import NoDataFound from "@/components/NoDataFound/NoDataFound";
import CategoryDetail from "./CategoryDetail";

interface CategoryProps {
  dataCategory: any;
  foundCategory: Category | null;
  setId: React.Dispatch<React.SetStateAction<string>>;
  id: string;
  handleGetCategoryDetail: (id: string | null) => Promise<void>;
  setFoundCategory: React.Dispatch<React.SetStateAction<Category | null>>;
}

const CategorySearch: React.FC<CategoryProps> = ({
  dataCategory,
  setId,
  id,
  foundCategory,
  handleGetCategoryDetail,
  setFoundCategory,
}) => {
  const [isFilterOpen, setIsFilterOpen] = useState(false);
  const [activeFilters, setActiveFilters] = useState(0);
  const [searchValue, setSearchValue] = useState<string>("");
  // Track active filters count
  useEffect(() => {
    let count = 0;
    if (id) count++;
    setActiveFilters(count);
  }, [id]);

  // Clear all filters
  const handleClearAll = () => {
    setId("");
    setSearchValue("");
    setFoundCategory(null);
  };

  return (
    <Card className="w-full mt-8 mb-6 shadow-md border border-gray-100">
      <Group p="apart" className="mb-4">
        <Group>
          <Title order={4} className="font-medium">
            Filter
          </Title>
          {activeFilters > 0 && (
            <Badge size="md" radius="sm" className="bg-blue-100 text-blue-700">
              {activeFilters} active filter{activeFilters !== 1 ? "s" : ""}
            </Badge>
          )}
        </Group>

        <Group>
          {activeFilters > 0 && (
            <Tooltip label="Clear all filters" position="bottom">
              <ActionIcon
                color="gray"
                variant="subtle"
                onClick={handleClearAll}
                className="hover:bg-gray-100"
              >
                <IconX size={18} />
              </ActionIcon>
            </Tooltip>
          )}
          <Tooltip
            label={isFilterOpen ? "Hide filters" : "Show filters"}
            position="bottom"
          >
            <Button
              variant="subtle"
              color="blue"
              leftSection={<IconFilter size={16} />}
              onClick={() => setIsFilterOpen(!isFilterOpen)}
              className="hover:bg-blue-50"
            >
              {isFilterOpen ? "Hide Filters" : "Show Filters"}
            </Button>
          </Tooltip>
        </Group>
      </Group>

      <Transition mounted={isFilterOpen} transition="fade" duration={200}>
        {(styles) => (
          <div style={styles}>
            <Divider className="mb-4" />

            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <Text size="sm" w={500} className="mb-1 text-gray-700">
                  Job Family Name
                </Text>
                <Select
                  placeholder="Search by  name..."
                  size="md"
                  radius="md"
                  searchable
                  clearable
                  limit={10}
                  leftSection={<IconUser size={16} className="text-gray-500" />}
                  className="shadow-sm"
                  searchValue={searchValue}
                  styles={{
                    input: {
                      fontWeight: 500,
                    },
                  }}
                  onChange={(value) => {
                    setId(value || "");
                    const selectedOption = dataCategory.find(
                      (opt: any) => opt.value == value
                    );
                    setSearchValue(selectedOption ? selectedOption.label : "");
                    handleGetCategoryDetail(value);
                  }}
                  value={id}
                  data={dataCategory}
                  // onKeyDown={(event) => {
                  //   if (event.key === "Enter") {
                  //     handleGetCategoryDetail(id);
                  //   }
                  // }}
                />
              </div>
            </div>
          </div>
        )}
      </Transition>

      <Group p="apart" className="mt-4">
        <div>
          {activeFilters > 0 && (
            <Text size="sm" c="dimmed" className="italic">
              {activeFilters} filter{activeFilters !== 1 ? "s" : ""} applied
            </Text>
          )}
        </div>

        <Group p="sm">
          {activeFilters > 0 && (
            <Button
              variant="subtle"
              color="gray"
              leftSection={<IconRefresh size={16} />}
              onClick={handleClearAll}
              className="hover:bg-gray-100"
              size="md"
            >
              Reset
            </Button>
          )}

          <Button
            radius="md"
            size="md"
            leftSection={<IconSearch size={16} />}
            className="bg-blue-600 hover:bg-blue-700 transition-colors shadow-md"
            onClick={() => handleGetCategoryDetail(id)}
            disabled={activeFilters === 0}
          >
            Search
          </Button>
        </Group>
      </Group>
      <Group>
        <div className="flex-1 overflow-auto mb-4">
          {foundCategory ? (
            <CategoryDetail category={foundCategory} />
          ) : (
            <NoDataFound />
          )}
        </div>
      </Group>
    </Card>
  );
};

export default CategorySearch;
