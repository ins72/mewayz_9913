import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  PlusIcon,
  PlayIcon,
  PauseIcon,
  DocumentIcon,
  QuestionMarkCircleIcon,
  AcademicCapIcon,
  ChatBubbleBottomCenterTextIcon,
  UserGroupIcon,
  ChartBarIcon,
  Cog6ToothIcon,
  VideoCameraIcon,
  MicrophoneIcon,
  PhotoIcon,
  LinkIcon,
  ClockIcon,
  CheckCircleIcon,
  XCircleIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  ArrowUpIcon,
  ArrowDownIcon
} from '@heroicons/react/24/outline';

const ProfessionalCourseBuilder = () => {
  const [activeTab, setActiveTab] = useState('content');
  const [course, setCourse] = useState({
    id: '1',
    title: 'Complete Digital Marketing Mastery',
    description: 'Learn everything you need to know about digital marketing from basics to advanced strategies',
    thumbnail: 'https://images.unsplash.com/photo-1611224923853-80b023f02d71?w=400&h=225&fit=crop',
    category: 'marketing',
    level: 'beginner',
    price: 99.99,
    currency: 'USD',
    language: 'english',
    duration: 0, // Will be calculated from modules
    published: false,
    enrolled_count: 0,
    rating: 0,
    completion_rate: 0
  });

  const [modules, setModules] = useState([
    {
      id: '1',
      title: 'Introduction to Digital Marketing',
      description: 'Get started with the fundamentals of digital marketing',
      order: 1,
      lessons: [
        {
          id: '1-1',
          title: 'What is Digital Marketing?',
          type: 'video',
          content: 'https://example.com/video1.mp4',
          duration: 480, // in seconds
          description: 'An overview of digital marketing and its importance in today\'s business world',
          completed: false,
          quiz: null
        },
        {
          id: '1-2',
          title: 'Digital Marketing Channels',
          type: 'video',
          content: 'https://example.com/video2.mp4',
          duration: 720,
          description: 'Explore the various digital marketing channels available',
          completed: false,
          quiz: {
            id: 'quiz-1-2',
            title: 'Digital Marketing Channels Quiz',
            questions: [
              {
                id: 'q1',
                question: 'Which of the following is a digital marketing channel?',
                type: 'multiple_choice',
                options: ['Social Media', 'Email', 'Content Marketing', 'All of the above'],
                correct_answer: 3,
                points: 10
              }
            ]
          }
        },
        {
          id: '1-3',
          title: 'Setting Up Analytics',
          type: 'text',
          content: '<h2>Setting Up Google Analytics</h2><p>Learn how to set up and configure Google Analytics...</p>',
          duration: 300,
          description: 'Step-by-step guide to setting up Google Analytics',
          completed: false,
          quiz: null
        }
      ]
    },
    {
      id: '2',
      title: 'Content Marketing Strategy',
      description: 'Create compelling content that drives engagement and conversions',
      order: 2,
      lessons: [
        {
          id: '2-1',
          title: 'Content Marketing Fundamentals',
          type: 'video',
          content: 'https://example.com/video3.mp4',
          duration: 600,
          description: 'Learn the core principles of content marketing',
          completed: false,
          quiz: null
        },
        {
          id: '2-2',
          title: 'Creating a Content Calendar',
          type: 'text',
          content: '<h2>Content Calendar Basics</h2><p>A content calendar helps you plan and organize...</p>',
          duration: 420,
          description: 'How to plan and organize your content strategy',
          completed: false,
          quiz: null
        }
      ]
    }
  ]);

  const [selectedModule, setSelectedModule] = useState(null);
  const [selectedLesson, setSelectedLesson] = useState(null);
  const [showModuleModal, setShowModuleModal] = useState(false);
  const [showLessonModal, setShowLessonModal] = useState(false);
  const [showQuizBuilder, setShowQuizBuilder] = useState(false);

  // Calculate total course duration
  useEffect(() => {
    const totalDuration = modules.reduce((total, module) => {
      const moduleDuration = module.lessons.reduce((moduleTotal, lesson) => {
        return moduleTotal + lesson.duration;
      }, 0);
      return total + moduleDuration;
    }, 0);

    setCourse(prev => ({ ...prev, duration: totalDuration }));
  }, [modules]);

  const formatDuration = (seconds) => {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    if (hours > 0) {
      return `${hours}h ${minutes}m`;
    }
    return `${minutes}m`;
  };

  const addModule = () => {
    const newModule = {
      id: Date.now().toString(),
      title: 'New Module',
      description: 'Module description',
      order: modules.length + 1,
      lessons: []
    };
    setModules([...modules, newModule]);
    setSelectedModule(newModule);
    setShowModuleModal(true);
  };

  const updateModule = (moduleId, updates) => {
    setModules(modules.map(module =>
      module.id === moduleId ? { ...module, ...updates } : module
    ));
  };

  const deleteModule = (moduleId) => {
    setModules(modules.filter(module => module.id !== moduleId));
  };

  const addLesson = (moduleId) => {
    const newLesson = {
      id: `${moduleId}-${Date.now()}`,
      title: 'New Lesson',
      type: 'video',
      content: '',
      duration: 300,
      description: 'Lesson description',
      completed: false,
      quiz: null
    };

    setModules(modules.map(module =>
      module.id === moduleId
        ? { ...module, lessons: [...module.lessons, newLesson] }
        : module
    ));

    setSelectedLesson(newLesson);
    setShowLessonModal(true);
  };

  const updateLesson = (moduleId, lessonId, updates) => {
    setModules(modules.map(module =>
      module.id === moduleId
        ? {
            ...module,
            lessons: module.lessons.map(lesson =>
              lesson.id === lessonId ? { ...lesson, ...updates } : lesson
            )
          }
        : module
    ));
  };

  const deleteLesson = (moduleId, lessonId) => {
    setModules(modules.map(module =>
      module.id === moduleId
        ? { ...module, lessons: module.lessons.filter(lesson => lesson.id !== lessonId) }
        : module
    ));
  };

  const moveModule = (moduleId, direction) => {
    const moduleIndex = modules.findIndex(m => m.id === moduleId);
    if (
      (direction === 'up' && moduleIndex > 0) ||
      (direction === 'down' && moduleIndex < modules.length - 1)
    ) {
      const newModules = [...modules];
      const targetIndex = direction === 'up' ? moduleIndex - 1 : moduleIndex + 1;
      [newModules[moduleIndex], newModules[targetIndex]] = [newModules[targetIndex], newModules[moduleIndex]];
      
      // Update order numbers
      newModules.forEach((module, index) => {
        module.order = index + 1;
      });
      
      setModules(newModules);
    }
  };

  const getLessonIcon = (type) => {
    switch (type) {
      case 'video': return VideoCameraIcon;
      case 'audio': return MicrophoneIcon;
      case 'text': return DocumentIcon;
      case 'image': return PhotoIcon;
      case 'link': return LinkIcon;
      default: return DocumentIcon;
    }
  };

  const ModuleCard = ({ module }) => (
    <div className="bg-card border border-default rounded-xl p-6 mb-6">
      <div className="flex items-center justify-between mb-4">
        <div className="flex-1">
          <div className="flex items-center space-x-3 mb-2">
            <span className="bg-accent-primary text-white px-2 py-1 rounded text-sm font-medium">
              Module {module.order}
            </span>
            <h3 className="text-lg font-semibold text-primary">{module.title}</h3>
          </div>
          <p className="text-secondary text-sm">{module.description}</p>
          <div className="flex items-center space-x-4 mt-2 text-xs text-secondary">
            <span>{module.lessons.length} lessons</span>
            <span>
              {formatDuration(module.lessons.reduce((total, lesson) => total + lesson.duration, 0))}
            </span>
          </div>
        </div>
        
        <div className="flex items-center space-x-2">
          <button
            onClick={() => moveModule(module.id, 'up')}
            disabled={module.order === 1}
            className="p-2 text-secondary hover:text-primary disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition-colors"
          >
            <ArrowUpIcon className="w-4 h-4" />
          </button>
          <button
            onClick={() => moveModule(module.id, 'down')}
            disabled={module.order === modules.length}
            className="p-2 text-secondary hover:text-primary disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition-colors"
          >
            <ArrowDownIcon className="w-4 h-4" />
          </button>
          <button
            onClick={() => {
              setSelectedModule(module);
              setShowModuleModal(true);
            }}
            className="p-2 text-blue-600 hover:text-blue-800 rounded-lg transition-colors"
          >
            <PencilIcon className="w-4 h-4" />
          </button>
          <button
            onClick={() => deleteModule(module.id)}
            className="p-2 text-red-600 hover:text-red-800 rounded-lg transition-colors"
          >
            <TrashIcon className="w-4 h-4" />
          </button>
        </div>
      </div>

      {/* Lessons */}
      <div className="space-y-3">
        {module.lessons.map((lesson, index) => {
          const LessonIcon = getLessonIcon(lesson.type);
          return (
            <div key={lesson.id} className="bg-surface border border-default rounded-lg p-4">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-3 flex-1">
                  <div className="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <LessonIcon className="w-4 h-4 text-blue-600" />
                  </div>
                  <div className="flex-1">
                    <h4 className="text-sm font-medium text-primary">{lesson.title}</h4>
                    <div className="flex items-center space-x-3 text-xs text-secondary mt-1">
                      <span className="capitalize">{lesson.type}</span>
                      <span>{formatDuration(lesson.duration)}</span>
                      {lesson.quiz && (
                        <span className="text-green-600">Has Quiz</span>
                      )}
                    </div>
                  </div>
                </div>
                
                <div className="flex items-center space-x-2">
                  {lesson.quiz && (
                    <button
                      onClick={() => {
                        setSelectedLesson(lesson);
                        setShowQuizBuilder(true);
                      }}
                      className="p-1 text-green-600 hover:text-green-800 rounded"
                    >
                      <QuestionMarkCircleIcon className="w-4 h-4" />
                    </button>
                  )}
                  <button
                    onClick={() => {
                      setSelectedLesson(lesson);
                      setShowLessonModal(true);
                    }}
                    className="p-1 text-blue-600 hover:text-blue-800 rounded"
                  >
                    <PencilIcon className="w-4 h-4" />
                  </button>
                  <button
                    onClick={() => deleteLesson(module.id, lesson.id)}
                    className="p-1 text-red-600 hover:text-red-800 rounded"
                  >
                    <TrashIcon className="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>
          );
        })}
        
        <button
          onClick={() => addLesson(module.id)}
          className="w-full border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-secondary hover:border-accent-primary hover:text-accent-primary transition-colors flex items-center justify-center space-x-2"
        >
          <PlusIcon className="w-5 h-5" />
          <span>Add Lesson</span>
        </button>
      </div>
    </div>
  );

  return (
    <div className="min-h-screen bg-app">
      <div className="max-w-6xl mx-auto px-4 py-8">
        {/* Header */}
        <div className="flex items-center justify-between mb-8">
          <div>
            <h1 className="text-3xl font-bold text-primary">Course Builder</h1>
            <p className="text-secondary mt-2">Create and manage your online courses</p>
          </div>
          
          <div className="flex items-center space-x-4">
            <button className="bg-surface border border-default text-primary px-4 py-2 rounded-lg hover:bg-hover transition-colors flex items-center space-x-2">
              <EyeIcon className="w-4 h-4" />
              <span>Preview</span>
            </button>
            
            <button className="bg-green-600 text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity flex items-center space-x-2">
              <CheckCircleIcon className="w-4 h-4" />
              <span>Publish Course</span>
            </button>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
          {/* Course Info Sidebar */}
          <div className="space-y-6">
            <div className="bg-card border border-default rounded-xl p-6">
              <img
                src={course.thumbnail}
                alt={course.title}
                className="w-full h-32 object-cover rounded-lg mb-4"
              />
              
              <h3 className="font-semibold text-primary mb-2">{course.title}</h3>
              <p className="text-sm text-secondary mb-4">{course.description}</p>
              
              <div className="space-y-2 text-sm">
                <div className="flex justify-between">
                  <span className="text-secondary">Price:</span>
                  <span className="font-medium text-primary">${course.price}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-secondary">Duration:</span>
                  <span className="font-medium text-primary">{formatDuration(course.duration)}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-secondary">Modules:</span>
                  <span className="font-medium text-primary">{modules.length}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-secondary">Lessons:</span>
                  <span className="font-medium text-primary">
                    {modules.reduce((total, module) => total + module.lessons.length, 0)}
                  </span>
                </div>
              </div>
            </div>

            {/* Course Stats */}
            <div className="bg-card border border-default rounded-xl p-6">
              <h3 className="font-semibold text-primary mb-4">Course Statistics</h3>
              <div className="space-y-3">
                <div className="flex items-center justify-between">
                  <span className="text-sm text-secondary">Enrolled</span>
                  <span className="text-sm font-medium text-primary">{course.enrolled_count}</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm text-secondary">Rating</span>
                  <span className="text-sm font-medium text-primary">{course.rating || 'N/A'}</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm text-secondary">Completion</span>
                  <span className="text-sm font-medium text-primary">{course.completion_rate}%</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm text-secondary">Status</span>
                  <span className={`text-sm font-medium ${course.published ? 'text-green-600' : 'text-orange-600'}`}>
                    {course.published ? 'Published' : 'Draft'}
                  </span>
                </div>
              </div>
            </div>
          </div>

          {/* Main Content */}
          <div className="lg:col-span-3">
            {/* Tabs */}
            <div className="flex space-x-1 bg-card border border-default rounded-lg p-1 mb-8">
              {['content', 'settings', 'students', 'analytics'].map(tab => (
                <button
                  key={tab}
                  onClick={() => setActiveTab(tab)}
                  className={`flex-1 px-4 py-2 rounded text-sm font-medium transition-colors ${
                    activeTab === tab 
                      ? 'bg-accent-primary text-white' 
                      : 'text-secondary hover:text-primary'
                  }`}
                >
                  {tab.charAt(0).toUpperCase() + tab.slice(1)}
                </button>
              ))}
            </div>

            {/* Tab Content */}
            {activeTab === 'content' && (
              <div>
                <div className="flex items-center justify-between mb-6">
                  <h2 className="text-2xl font-bold text-primary">Course Content</h2>
                  <button
                    onClick={addModule}
                    className="bg-accent-primary text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity flex items-center space-x-2"
                  >
                    <PlusIcon className="w-4 h-4" />
                    <span>Add Module</span>
                  </button>
                </div>

                {modules.length === 0 ? (
                  <div className="text-center py-12 bg-card border border-default rounded-xl">
                    <AcademicCapIcon className="w-16 h-16 mx-auto text-secondary mb-4 opacity-50" />
                    <h3 className="text-lg font-medium text-primary mb-2">No modules yet</h3>
                    <p className="text-secondary mb-4">Start building your course by adding your first module</p>
                    <button
                      onClick={addModule}
                      className="bg-accent-primary text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity"
                    >
                      Add First Module
                    </button>
                  </div>
                ) : (
                  <div>
                    {modules.map(module => (
                      <ModuleCard key={module.id} module={module} />
                    ))}
                  </div>
                )}
              </div>
            )}

            {activeTab === 'settings' && (
              <div className="bg-card border border-default rounded-xl p-6">
                <h2 className="text-2xl font-bold text-primary mb-6">Course Settings</h2>
                
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">Course Title</label>
                    <input
                      type="text"
                      value={course.title}
                      onChange={(e) => setCourse({ ...course, title: e.target.value })}
                      className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">Category</label>
                    <select
                      value={course.category}
                      onChange={(e) => setCourse({ ...course, category: e.target.value })}
                      className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                    >
                      <option value="marketing">Marketing</option>
                      <option value="development">Development</option>
                      <option value="design">Design</option>
                      <option value="business">Business</option>
                      <option value="photography">Photography</option>
                    </select>
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">Difficulty Level</label>
                    <select
                      value={course.level}
                      onChange={(e) => setCourse({ ...course, level: e.target.value })}
                      className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                    >
                      <option value="beginner">Beginner</option>
                      <option value="intermediate">Intermediate</option>
                      <option value="advanced">Advanced</option>
                    </select>
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-secondary mb-2">Price ($)</label>
                    <input
                      type="number"
                      value={course.price}
                      onChange={(e) => setCourse({ ...course, price: parseFloat(e.target.value) })}
                      className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                      min="0"
                      step="0.01"
                    />
                  </div>

                  <div className="md:col-span-2">
                    <label className="block text-sm font-medium text-secondary mb-2">Course Description</label>
                    <textarea
                      value={course.description}
                      onChange={(e) => setCourse({ ...course, description: e.target.value })}
                      rows={4}
                      className="w-full px-3 py-2 border border-default rounded-lg bg-surface text-primary focus:border-accent-primary focus:ring-2 focus:ring-accent-primary/20 outline-none"
                    />
                  </div>
                </div>

                <div className="mt-6 pt-6 border-t border-default">
                  <button className="bg-green-600 text-white px-6 py-2 rounded-lg hover:opacity-90 transition-opacity">
                    Save Settings
                  </button>
                </div>
              </div>
            )}

            {activeTab === 'students' && (
              <div className="bg-card border border-default rounded-xl p-6">
                <h2 className="text-2xl font-bold text-primary mb-6">Students</h2>
                <div className="text-center py-8">
                  <UserGroupIcon className="w-16 h-16 mx-auto text-secondary mb-4 opacity-50" />
                  <p className="text-secondary">No students enrolled yet</p>
                </div>
              </div>
            )}

            {activeTab === 'analytics' && (
              <div className="bg-card border border-default rounded-xl p-6">
                <h2 className="text-2xl font-bold text-primary mb-6">Course Analytics</h2>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div className="bg-surface border border-default rounded-lg p-4 text-center">
                    <div className="text-3xl font-bold text-primary mb-2">0</div>
                    <div className="text-sm text-secondary">Total Views</div>
                  </div>
                  <div className="bg-surface border border-default rounded-lg p-4 text-center">
                    <div className="text-3xl font-bold text-primary mb-2">0</div>
                    <div className="text-sm text-secondary">Enrollments</div>
                  </div>
                  <div className="bg-surface border border-default rounded-lg p-4 text-center">
                    <div className="text-3xl font-bold text-primary mb-2">$0</div>
                    <div className="text-sm text-secondary">Revenue</div>
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProfessionalCourseBuilder;