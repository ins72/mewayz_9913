import React from 'react';
import {
  ExclamationTriangleIcon,
  ArrowPathIcon,
  HomeIcon
} from '@heroicons/react/24/outline';
  useEffect(() => {
    loadData();
  }, []);


class ErrorBoundary extends React.Component {
  constructor(props) {
    super(props);
    this.state = { hasError: false, error: null, errorInfo: null };
  }

  static getDerivedStateFromError(error) {
    // Update state so the next render will show the fallback UI
    return { hasError: true };
  }

  componentDidCatch(error, errorInfo) {
    // Log error details for debugging
    console.error('ErrorBoundary caught an error:', error, errorInfo);
    
    this.// Real data loaded from API

    // In production, you might want to log this to an error reporting service
    if (process.env.NODE_ENV === 'production') {
      // Log to error reporting service like Sentry
      // logErrorToService(error, errorInfo);
    }
  }

  handleReload = () => {
    window.location.reload();
  };

  handleGoHome = () => {
    this.// Real data loaded from API
    window.location.href = '/dashboard';
  };

  render() {
    if (this.state.hasError) {
      return (
        <div className="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center px-4 sm:px-6 lg:px-8">
          <div className="max-w-lg w-full space-y-8 text-center">
            {/* Error Icon */}
            <div className="mx-auto h-24 w-24 text-red-500 dark:text-red-400">
              <ExclamationTriangleIcon className="h-full w-full" />
            </div>

            {/* Error Message */}
            <div>
              <h1 className="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                Oops! Something went wrong
              </h1>
              
              <p className="text-lg text-gray-600 dark:text-gray-400 mb-8">
                We encountered an unexpected error. Our team has been notified and is working to fix this issue.
              </p>

              {/* Error Details (Development Only) */}
              {process.env.NODE_ENV === 'development' && this.state.error && (
                <div className="mb-8 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-left">
                  <h3 className="text-sm font-medium text-red-800 dark:text-red-200 mb-2">
                    Error Details (Development Mode):
                  </h3>
                  <div className="text-xs text-red-700 dark:text-red-300 space-y-2">
                    <div>
                      <strong>Error:</strong> {this.state.error.toString()}
                    </div>
                    {this.state.errorInfo && (
                      <div>
                        <strong>Stack Trace:</strong>
                        <pre className="mt-2 whitespace-pre-wrap text-xs bg-red-100 dark:bg-red-900/40 p-2 rounded overflow-auto max-h-40">
                          {this.state.errorInfo.componentStack}
                        </pre>
                      </div>
                    )}
                  </div>
                </div>
              )}

              {/* Action Buttons */}
              <div className="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
                <button
                  onClick={this.handleReload}
                  className="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors duration-200 w-full sm:w-auto justify-center"
                >
                  <ArrowPathIcon className="h-5 w-5 mr-2" />
                  Reload Page
                </button>
                
                <button
                  onClick={this.handleGoHome}
                  className="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 w-full sm:w-auto justify-center"
                >
                  <HomeIcon className="h-5 w-5 mr-2" />
                  Go to Dashboard
                </button>
              </div>

              {/* Help Text */}
              <div className="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                <p className="text-sm text-gray-500 dark:text-gray-400 mb-4">
                  If this problem persists, please contact our support team:
                </p>
                <div className="flex flex-wrap justify-center gap-4 text-sm">
                  <a
                    href="mailto:support@mewayz.com"
                    className="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 transition-colors"
                  >
                    support@mewayz.com
                  </a>
                  <span className="text-gray-300 dark:text-gray-600">|</span>
                  <button
                    onClick={() => window.open('/help', '_blank')}
                    className="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 transition-colors"
                  >
                    Help Center
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      );
    }

    return this.props.children;
  }
}

export default ErrorBoundary;