import 'package:flutter/material.dart';
import '../../config/theme.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/custom_button.dart';

class CourseCreationPlatformScreen extends StatefulWidget {
  const CourseCreationPlatformScreen({super.key});

  @override
  State<CourseCreationPlatformScreen> createState() => _CourseCreationPlatformScreenState();
}

class _CourseCreationPlatformScreenState extends State<CourseCreationPlatformScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 4, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Courses & Community'),
        backgroundColor: AppColors.background,
        foregroundColor: AppColors.textPrimary,
        elevation: 0,
        bottom: TabBar(
          controller: _tabController,
          labelColor: AppColors.primary,
          unselectedLabelColor: AppColors.textSecondary,
          indicatorColor: AppColors.primary,
          tabs: const [
            Tab(text: 'Courses'),
            Tab(text: 'Students'),
            Tab(text: 'Community'),
            Tab(text: 'Analytics'),
          ],
        ),
      ),
      drawer: const AppDrawer(),
      body: TabBarView(
        controller: _tabController,
        children: const [
          CoursesTab(),
          StudentsTab(),
          CommunityTab(),
          CourseAnalyticsTab(),
        ],
      ),
    );
  }
}

class CoursesTab extends StatelessWidget {
  const CoursesTab({super.key});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Text(
                'Your Courses',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              const Spacer(),
              CustomButton(
                text: 'Create Course',
                onPressed: () {
                  // TODO: Navigate to course creation
                },
                type: ButtonType.primary,
                width: 130,
                height: 36,
              ),
            ],
          ),
          const SizedBox(height: 16),
          
          // Course Stats
          Row(
            children: [
              Expanded(
                child: _buildStatCard('Total Courses', '8', Icons.school),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: _buildStatCard('Published', '5', Icons.public),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: _buildStatCard('Students', '247', Icons.people),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: _buildStatCard('Revenue', '\$3,240', Icons.attach_money),
              ),
            ],
          ),
          
          const SizedBox(height: 24),
          
          // Courses List
          Expanded(
            child: ListView(
              children: [
                _buildCourseCard(
                  'Flutter Development Masterclass',
                  'Learn Flutter from scratch to advanced',
                  'Published',
                  '89 students',
                  '12 lessons',
                  AppColors.success,
                ),
                const SizedBox(height: 12),
                _buildCourseCard(
                  'Digital Marketing Fundamentals',
                  'Complete guide to digital marketing',
                  'Published',
                  '124 students',
                  '18 lessons',
                  AppColors.success,
                ),
                const SizedBox(height: 12),
                _buildCourseCard(
                  'UI/UX Design Principles',
                  'Design beautiful user interfaces',
                  'Draft',
                  '0 students',
                  '8 lessons',
                  AppColors.warning,
                ),
                const SizedBox(height: 12),
                _buildCourseCard(
                  'JavaScript for Beginners',
                  'Learn JavaScript programming',
                  'Published',
                  '34 students',
                  '15 lessons',
                  AppColors.success,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatCard(String title, String value, IconData icon) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.secondaryBorder),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: AppColors.textSecondary, size: 20),
          const SizedBox(height: 8),
          Text(
            value,
            style: const TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          Text(
            title,
            style: const TextStyle(
              fontSize: 10,
              color: AppColors.textSecondary,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCourseCard(
    String title,
    String description,
    String status,
    String students,
    String lessons,
    Color statusColor,
  ) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.secondaryBorder),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              // Course Thumbnail
              Container(
                width: 80,
                height: 60,
                decoration: BoxDecoration(
                  color: AppColors.background,
                  borderRadius: BorderRadius.circular(8),
                ),
                child: const Icon(
                  Icons.play_circle_outline,
                  color: AppColors.textSecondary,
                  size: 32,
                ),
              ),
              const SizedBox(width: 16),
              
              // Course Info
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      description,
                      style: const TextStyle(
                        fontSize: 14,
                        color: AppColors.textSecondary,
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ],
                ),
              ),
              
              // Status
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: statusColor.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(4),
                ),
                child: Text(
                  status,
                  style: TextStyle(
                    fontSize: 12,
                    color: statusColor,
                    fontWeight: FontWeight.w500,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Icon(Icons.people, color: AppColors.textSecondary, size: 16),
              const SizedBox(width: 4),
              Text(
                students,
                style: const TextStyle(
                  fontSize: 12,
                  color: AppColors.textSecondary,
                ),
              ),
              const SizedBox(width: 16),
              Icon(Icons.video_library, color: AppColors.textSecondary, size: 16),
              const SizedBox(width: 4),
              Text(
                lessons,
                style: const TextStyle(
                  fontSize: 12,
                  color: AppColors.textSecondary,
                ),
              ),
              const Spacer(),
              CustomButton(
                text: 'Edit',
                onPressed: () {
                  // TODO: Navigate to course editor
                },
                type: ButtonType.secondary,
                width: 60,
                height: 32,
              ),
              const SizedBox(width: 8),
              CustomButton(
                text: 'View',
                onPressed: () {
                  // TODO: View course
                },
                type: ButtonType.primary,
                width: 60,
                height: 32,
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class StudentsTab extends StatelessWidget {
  const StudentsTab({super.key});

  @override
  Widget build(BuildContext context) {
    return const Center(
      child: Text(
        'Student Management\n(Coming Soon)',
        textAlign: TextAlign.center,
        style: TextStyle(
          color: AppColors.textSecondary,
          fontSize: 16,
        ),
      ),
    );
  }
}

class CommunityTab extends StatelessWidget {
  const CommunityTab({super.key});

  @override
  Widget build(BuildContext context) {
    return const Center(
      child: Text(
        'Community Features\n(Coming Soon)',
        textAlign: TextAlign.center,
        style: TextStyle(
          color: AppColors.textSecondary,
          fontSize: 16,
        ),
      ),
    );
  }
}

class CourseAnalyticsTab extends StatelessWidget {
  const CourseAnalyticsTab({super.key});

  @override
  Widget build(BuildContext context) {
    return const Center(
      child: Text(
        'Course Analytics\n(Coming Soon)',
        textAlign: TextAlign.center,
        style: TextStyle(
          color: AppColors.textSecondary,
          fontSize: 16,
        ),
      ),
    );
  }
}