type CardComponent = {
  completed: number;
  onProgress: number;
  cancelled: number;
};

const AssessmentOverview: React.FC<CardComponent> = ({
  completed,
  onProgress,
  cancelled,
}) => {
  return (
    <>
      <div className="bg-white rounded-lg shadow-md border border-gray-100 overflow-hidden">
        <div className="grid grid-cols-5">
          {/* Left panel with summary */}
          <div className="col-span-2 p-6 bg-gray-50 flex flex-col justify-between">
            <div>
              <h3 className="font-semibold text-gray-800 mb-2">
                Assessment Progress
              </h3>
              <p className="text-xs text-gray-500 mb-6">Status Overview</p>
            </div>

            <div className="space-y-3">
              <div className="flex items-center justify-between">
                <div className="flex items-center">
                  <div className="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                  <span className="text-xs text-gray-600">Completed</span>
                </div>
                <span className="text-sm font-medium">
                  {completed.toFixed(2)}%
                </span>
              </div>

              <div className="flex items-center justify-between">
                <div className="flex items-center">
                  <div className="w-3 h-3 rounded-full bg-yellow-400 mr-2"></div>
                  <span className="text-xs text-gray-600">In Progress</span>
                </div>
                <span className="text-sm font-medium">
                  {onProgress.toFixed(2)}%
                </span>
              </div>

              <div className="flex items-center justify-between">
                <div className="flex items-center">
                  <div className="w-3 h-3 rounded-full bg-red-400 mr-2"></div>
                  <span className="text-xs text-gray-600">Cancelled</span>
                </div>
                <span className="text-sm font-medium">
                  {cancelled.toFixed(2)}%
                </span>
              </div>
            </div>
          </div>

          {/* Right panel with visualizations */}
          <div className="col-span-3 p-6">
            {/* Circular progress */}
            <div className="flex justify-center mb-6">
              <div className="relative w-40 h-40">
                <svg viewBox="0 0 100 100" className="w-full h-full">
                  <circle
                    cx="50"
                    cy="50"
                    r="45"
                    fill="transparent"
                    stroke="#f3f4f6"
                    strokeWidth="10"
                  />

                  {/* Completed */}
                  <circle
                    cx="50"
                    cy="50"
                    r="45"
                    fill="transparent"
                    stroke="#22c55e"
                    strokeWidth="10"
                    strokeDasharray={`${2 * Math.PI * 45 * (completed / 100)} ${
                      2 * Math.PI * 45 * (1 - completed / 100)
                    }`}
                    transform="rotate(-90 50 50)"
                  />
                </svg>
                <div className="absolute inset-0 flex flex-col items-center justify-center">
                  <span className="text-3xl font-bold text-gray-700">
                    {completed.toFixed(2)}%
                  </span>
                  <span className="text-xs text-gray-500">Complete</span>
                </div>
              </div>
            </div>

            {/* Combined progress bar */}
            <div className="h-2 w-full bg-gray-100 rounded-full overflow-hidden mb-2">
              <div className="flex h-full">
                <div
                  className="bg-green-500 h-full"
                  style={{ width: `${completed}%` }}
                ></div>
                <div
                  className="bg-yellow-400 h-full"
                  style={{
                    width: `${onProgress}%`,
                  }}
                ></div>
                <div
                  className="bg-red-400 h-full"
                  style={{
                    width: `${cancelled}%`,
                  }}
                ></div>
              </div>
            </div>
            <div className="flex justify-between">
              <span className="text-xs text-gray-500">0%</span>
              <span className="text-xs text-gray-500">100%</span>
            </div>
          </div>
        </div>
      </div>
    </>
  );
};

export default AssessmentOverview;
