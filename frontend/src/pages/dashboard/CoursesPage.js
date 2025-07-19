import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  AcademicCapIcon, 
  PlusIcon, 
  PencilIcon, 
  TrashIcon,
  EyeIcon,
  UserGroupIcon,
  ChartBarIcon,
  PlayIcon,
  DocumentTextIcon,
  VideoCameraIcon,
  ClockIcon,
  StarIcon
} from '@heroicons/react/24/outline';
import Button from '../../components/Button';

const CoursesPage = () => {
  const [courses, setCourses] = useState([]);
  const [students, setStudents] = useState([]);
  const [analytics, setAnalytics] = useState(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('overview');

  useEffect(() => {
    loadCoursesData();
  }, []);

  const loadCoursesData = async () => {
    try {
      // Mock data for now - replace with actual API calls
      setCourses([
        {
          id: 1,
          title: 'Complete Digital Marketing Masterclass',
          description: 'Learn all aspects of digital marketing from SEO to social media',
          price: 199.99,
          students: 145,
          lessons: 24,
          duration: '8 hours',
          rating: 4.8,
          status: 'published',
          progress: 85
        },
        {
          id: 2,
          title: 'Instagram Growth Strategy Course',
          description: 'Grow your Instagram following organically with proven strategies',
          price: 99.99,
          students: 89,
          lessons: 12,
          duration: '4 hours',
          rating: 4.9,
          status: 'published',
          progress: 100
        },
        {
          id: 3,
          title: 'E-commerce Business Fundamentals',
          description: 'Start and scale your e-commerce business from scratch',
          price: 299.99,
          students: 67,
          lessons: 36,
          duration: '12 hours',
          rating: 4.7,
          status: 'draft',
          progress: 45
        }
      ]);

      setStudents([
        {
          id: 1,
          name: 'Sarah Johnson',
          email: 'sarah@example.com',
          coursesEnrolled: 2,
          totalProgress: 78,
          lastActivity: '2 hours ago'
        },
        {
          id: 2,
          name: 'Mike Chen',
          email: 'mike@example.com',
          coursesEnrolled: 1,
          totalProgress: 92,
          lastActivity: '1 day ago'
        },
        {
          id: 3,
          name: 'Emily Davis',
          email: 'emily@example.com',
          coursesEnrolled: 3,
          totalProgress: 65,
          lastActivity: '3 hours ago'
        }
      ]);

      setAnalytics({
        totalRevenue: 25670,
        totalStudents: 301,
        totalCourses: 8,
        averageRating: 4.8,
        completionRate: 72,
        topCourse: 'Digital Marketing Masterclass'
      });
    } catch (error) {
      console.error('Failed to load courses data:', error);
    } finally {
      setLoading(false);
    }
  };

  const StatCard = ({ title, value, change, icon: Icon, color = 'primary' }) => (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className="card-elevated p-6"
    >
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm font-medium text-secondary">{title}</p>
          <p className="text-3xl font-bold text-primary mt-2">{value}</p>
          {change && (
            <p className={`text-sm mt-2 ${change > 0 ? 'text-accent-success' : 'text-accent-danger'}`}>
              {change > 0 ? '+' : ''}{change}% vs last month
            </p>
          )}
        </div>
        <div className={`bg-gradient-${color} p-3 rounded-lg`}>
          <Icon className="w-8 h-8 text-white" />
        </div>
      </div>
    </motion.div>
  );

  const CourseCard = ({ course }) => (
    <div className="card-elevated p-6">
      <div className="flex items-start justify-between mb-4">
        <div className="flex-1">
          <div className="flex items-center justify-between mb-2">
            <h3 className="font-semibold text-primary">{course.title}</h3>
            <div className="flex items-center space-x-2">
              <button className="p-2 text-secondary hover:text-primary">
                <EyeIcon className="w-4 h-4" />
              </button>
              <button className="p-2 text-secondary hover:text-primary">
                <PencilIcon className="w-4 h-4" />
              </button>
              <button className="p-2 text-secondary hover:text-accent-danger">
                <TrashIcon className="w-4 h-4" />
              </button>
            </div>
          </div>
          <p className="text-secondary text-sm mb-4">{course.description}</p>
          
          <div className="grid grid-cols-2 gap-4 text-sm mb-4">
            <div className="flex items-center space-x-2">
              <UserGroupIcon className="w-4 h-4 text-accent-primary" />
              <span className="text-primary">{course.students} students</span>
            </div>
            <div className="flex items-center space-x-2">
              <DocumentTextIcon className="w-4 h-4 text-accent-primary" />
              <span className="text-primary">{course.lessons} lessons</span>
            </div>
            <div className="flex items-center space-x-2">
              <ClockIcon className="w-4 h-4 text-accent-primary" />
              <span className="text-primary">{course.duration}</span>
            </div>
            <div className="flex items-center space-x-2">
              <StarIcon className="w-4 h-4 text-accent-primary" />
              <span className="text-primary">{course.rating}/5</span>
            </div>
          </div>
          
          <div className="flex items-center justify-between">
            <div>
              <p className="text-2xl font-bold text-accent-primary">${course.price}</p>
              <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                course.status === 'published'
                  ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                  : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
              }`}>
                {course.status}
              </span>
            </div>
            <div className="text-right">
              <p className="text-sm text-secondary">Progress</p>
              <div className="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-1">
                <div 
                  className="bg-accent-primary h-2 rounded-full transition-all duration-300"
                  style={{ width: `${course.progress}%` }}
                ></div>
              </div>
              <p className="text-xs text-secondary mt-1">{course.progress}%</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );

  const StudentCard = ({ student }) => (
    <div className="card-elevated p-6">
      <div className="flex items-center justify-between mb-4">
        <div>
          <h3 className="font-semibold text-primary">{student.name}</h3>
          <p className="text-secondary text-sm">{student.email}</p>
        </div>
        <Button variant="secondary" size="small">Message</Button>
      </div>
      
      <div className="grid grid-cols-2 gap-4 text-sm mb-4">
        <div>
          <p className="text-secondary">Courses Enrolled</p>
          <p className="font-medium text-primary">{student.coursesEnrolled}</p>
        </div>
        <div>
          <p className="text-secondary">Overall Progress</p>
          <p className="font-medium text-primary">{student.totalProgress}%</p>
        </div>
      </div>
      
      <div className="mb-4">
        <p className="text-sm text-secondary mb-1">Progress</p>
        <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
          <div 
            className="bg-accent-success h-2 rounded-full transition-all duration-300"
            style={{ width: `${student.totalProgress}%` }}
          ></div>
        </div>
      </div>
      
      <p className="text-xs text-secondary">Last active: {student.lastActivity}</p>
    </div>
  );

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="spinner w-8 h-8 text-accent-primary"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-primary">Course Management</h1>
          <p className="text-secondary mt-1">Create and manage your online courses</p>
        </div>
        <div className="flex items-center space-x-3">
          <Button variant="secondary">
            <VideoCameraIcon className="w-4 h-4 mr-2" />
            Upload Content
          </Button>
          <Button>
            <PlusIcon className="w-4 h-4 mr-2" />
            Create Course
          </Button>
        </div>
      </div>

      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: 'overview', name: 'Overview' },
            { id: 'courses', name: 'Courses' },
            { id: 'students', name: 'Students' },
            { id: 'analytics', name: 'Analytics' }
          ].map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              className={`py-2 px-1 border-b-2 font-medium text-sm ${
                activeTab === tab.id
                  ? 'border-accent-primary text-accent-primary'
                  : 'border-transparent text-secondary hover:text-primary hover:border-gray-300'
              }`}
            >
              {tab.name}
            </button>
          ))}
        </nav>
      </div>

      {/* Content based on active tab */}
      {activeTab === 'overview' && (
        <div className="space-y-6">
          {/* Analytics Stats */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard
              title="Total Revenue"
              value={`$${analytics.totalRevenue.toLocaleString()}`}
              change={15.2}
              icon={ChartBarIcon}
              color="primary"
            />
            <StatCard
              title="Total Students"
              value={analytics.totalStudents.toLocaleString()}
              change={12.8}
              icon={UserGroupIcon}
              color="success"
            />
            <StatCard
              title="Total Courses"
              value={analytics.totalCourses.toString()}
              change={8.1}
              icon={AcademicCapIcon}
              color="warning"
            />
            <StatCard
              title="Completion Rate"
              value={`${analytics.completionRate}%`}
              change={5.3}
              icon={StarIcon}
              color="primary"
            />
          </div>

          {/* Quick Actions */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <PlayIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Record Lesson</h3>
              <p className="text-secondary">Create new video lessons for your courses</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <DocumentTextIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Add Assignment</h3>
              <p className="text-secondary">Create assignments and quizzes</p>
            </button>
            <button className="card-elevated p-6 text-left hover-surface transition-colors">
              <UserGroupIcon className="w-8 h-8 text-accent-primary mb-4" />
              <h3 className="font-semibold text-primary mb-2">Student Analytics</h3>
              <p className="text-secondary">Track student progress and engagement</p>
            </button>
          </div>
        </div>
      )}

      {activeTab === 'courses' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">My Courses</h2>
            <div className="flex items-center space-x-3">
              <select className="input px-3 py-2 rounded-md">
                <option>All Courses</option>
                <option>Published</option>
                <option>Draft</option>
                <option>Archived</option>
              </select>
              <select className="input px-3 py-2 rounded-md">
                <option>Sort by Date</option>
                <option>Sort by Students</option>
                <option>Sort by Revenue</option>
              </select>
            </div>
          </div>
          
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {courses.map((course) => (
              <CourseCard key={course.id} course={course} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'students' && (
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-semibold text-primary">Students</h2>
            <div className="flex items-center space-x-3">
              <input 
                type="text" 
                placeholder="Search students..."
                className="input px-3 py-2 rounded-md"
              />
              <select className="input px-3 py-2 rounded-md">
                <option>All Students</option>
                <option>Active</option>
                <option>Completed</option>
                <option>At Risk</option>
              </select>
            </div>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {students.map((student) => (
              <StudentCard key={student.id} student={student} />
            ))}
          </div>
        </div>
      )}

      {activeTab === 'analytics' && (
        <div className="space-y-6">
          <h2 className="text-xl font-semibold text-primary">Course Analytics</h2>
          <div className="card-elevated p-8 text-center">
            <ChartBarIcon className="w-16 h-16 text-accent-primary mx-auto mb-4" />
            <h3 className="text-lg font-semibold text-primary mb-2">Detailed Analytics Coming Soon</h3>
            <p className="text-secondary">We're building comprehensive course analytics to help you track student progress, engagement, and course performance.</p>
          </div>
        </div>
      )}
    </div>
  );
};

export default CoursesPage;