const DetailRow = ({
  label,
  value,
  onExtraClick,
}: {
  label: string;
  value: string | undefined;
  onExtraClick?: () => void;
}) => (
  <div className="flex items-center justify-between space-x-3 p-2 bg-gray-50 dark:bg-neutral-800 rounded-lg">
    <div>
      <span className="text-sm font-medium text-neutral-600 dark:text-neutral-300 block">
        {label}
      </span>
      <p className="text-neutral-800 dark:text-neutral-100 font-semibold">
        {value || "N/A"}
      </p>
    </div>
    {onExtraClick && (
      <button
        onClick={onExtraClick}
        className="text-blue-500 hover:bg-blue-100 dark:hover:bg-blue-900 p-2 rounded-full transition-colors"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          className="h-5 w-5"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
        >
          <path
            strokeLinecap="round"
            strokeLinejoin="round"
            strokeWidth={2}
            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
      </button>
    )}
  </div>
);

export default DetailRow;
