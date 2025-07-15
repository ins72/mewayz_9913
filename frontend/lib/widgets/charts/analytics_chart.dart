import 'package:flutter/material.dart';
import '../../config/colors.dart';

class AnalyticsChart extends StatelessWidget {
  const AnalyticsChart({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      child: Column(
        children: [
          _buildLegend(),
          const SizedBox(height: 16),
          Expanded(
            child: _buildChart(),
          ),
        ],
      ),
    );
  }

  Widget _buildLegend() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
      children: [
        _buildLegendItem('Views', const Color(0xFF4ECDC4)),
        _buildLegendItem('Clicks', const Color(0xFF45B7D1)),
        _buildLegendItem('Revenue', const Color(0xFF26DE81)),
      ],
    );
  }

  Widget _buildLegendItem(String label, Color color) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          width: 12,
          height: 12,
          decoration: BoxDecoration(
            color: color,
            borderRadius: BorderRadius.circular(2),
          ),
        ),
        const SizedBox(width: 8),
        Text(
          label,
          style: const TextStyle(
            fontSize: 12,
            color: AppColors.textSecondary,
          ),
        ),
      ],
    );
  }

  Widget _buildChart() {
    return CustomPaint(
      painter: ChartPainter(),
      child: Container(),
    );
  }
}

class ChartPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..style = PaintingStyle.stroke
      ..strokeWidth = 3;

    // Sample data points
    final viewsData = [20, 45, 30, 60, 40, 80, 65];
    final clicksData = [15, 30, 25, 45, 35, 60, 50];
    final revenueData = [10, 25, 20, 35, 30, 50, 45];

    // Draw chart lines
    _drawLine(canvas, size, viewsData, const Color(0xFF4ECDC4), paint);
    _drawLine(canvas, size, clicksData, const Color(0xFF45B7D1), paint);
    _drawLine(canvas, size, revenueData, const Color(0xFF26DE81), paint);

    // Draw grid lines
    _drawGridLines(canvas, size);
  }

  void _drawLine(Canvas canvas, Size size, List<double> data, Color color, Paint paint) {
    paint.color = color;
    final path = Path();
    
    for (int i = 0; i < data.length; i++) {
      final x = (i / (data.length - 1)) * size.width;
      final y = size.height - (data[i] / 100) * size.height;
      
      if (i == 0) {
        path.moveTo(x, y);
      } else {
        path.lineTo(x, y);
      }
    }
    
    canvas.drawPath(path, paint);
  }

  void _drawGridLines(Canvas canvas, Size size) {
    final gridPaint = Paint()
      ..color = AppColors.border
      ..strokeWidth = 1;

    // Horizontal grid lines
    for (int i = 0; i <= 5; i++) {
      final y = (i / 5) * size.height;
      canvas.drawLine(
        Offset(0, y),
        Offset(size.width, y),
        gridPaint,
      );
    }

    // Vertical grid lines
    for (int i = 0; i <= 6; i++) {
      final x = (i / 6) * size.width;
      canvas.drawLine(
        Offset(x, 0),
        Offset(x, size.height),
        gridPaint,
      );
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}