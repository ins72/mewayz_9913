import 'package:flutter/material.dart';
import '../config/colors.dart';

class LogoWidget extends StatefulWidget {
  final double size;
  final bool showText;
  final Color? color;

  const LogoWidget({
    super.key,
    this.size = 60,
    this.showText = true,
    this.color,
  });

  @override
  State<LogoWidget> createState() => _LogoWidgetState();
}

class _LogoWidgetState extends State<LogoWidget> with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _rotationAnimation;
  late Animation<double> _scaleAnimation;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      duration: const Duration(milliseconds: 2000),
      vsync: this,
    );
    
    _rotationAnimation = Tween<double>(
      begin: 0,
      end: 1,
    ).animate(CurvedAnimation(
      parent: _controller,
      curve: const Interval(0.0, 0.7, curve: Curves.easeInOut),
    ));
    
    _scaleAnimation = Tween<double>(
      begin: 0.8,
      end: 1.0,
    ).animate(CurvedAnimation(
      parent: _controller,
      curve: const Interval(0.3, 1.0, curve: Curves.elasticOut),
    ));
    
    _controller.forward();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final logoColor = widget.color ?? AppColors.primary;
    
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        AnimatedBuilder(
          animation: _controller,
          builder: (context, child) {
            return Transform.scale(
              scale: _scaleAnimation.value,
              child: Transform.rotate(
                angle: _rotationAnimation.value * 0.1,
                child: Container(
                  width: widget.size,
                  height: widget.size,
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                      colors: [
                        logoColor,
                        logoColor.withOpacity(0.8),
                      ],
                    ),
                    borderRadius: BorderRadius.circular(widget.size * 0.2),
                    boxShadow: [
                      BoxShadow(
                        color: logoColor.withOpacity(0.3),
                        blurRadius: 20,
                        offset: const Offset(0, 8),
                      ),
                    ],
                  ),
                  child: Stack(
                    alignment: Alignment.center,
                    children: [
                      // M Shape
                      CustomPaint(
                        size: Size(widget.size * 0.6, widget.size * 0.6),
                        painter: MLogoPainter(
                          color: AppColors.onPrimary,
                        ),
                      ),
                      // Accent dot
                      Positioned(
                        top: widget.size * 0.25,
                        right: widget.size * 0.25,
                        child: Container(
                          width: widget.size * 0.1,
                          height: widget.size * 0.1,
                          decoration: BoxDecoration(
                            color: AppColors.onPrimary,
                            shape: BoxShape.circle,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            );
          },
        ),
        if (widget.showText) ...[
          const SizedBox(height: 12),
          Text(
            'MEWAYZ',
            style: TextStyle(
              fontSize: widget.size * 0.25,
              fontWeight: FontWeight.bold,
              color: logoColor,
              letterSpacing: 1.5,
            ),
          ),
        ],
      ],
    );
  }
}

class MLogoPainter extends CustomPainter {
  final Color color;

  MLogoPainter({required this.color});

  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = color
      ..style = PaintingStyle.fill;

    final path = Path();
    
    // Draw stylized M
    final width = size.width;
    final height = size.height;
    
    // Left vertical line
    path.moveTo(width * 0.1, height * 0.8);
    path.lineTo(width * 0.1, height * 0.2);
    path.lineTo(width * 0.25, height * 0.2);
    path.lineTo(width * 0.25, height * 0.65);
    
    // First diagonal
    path.lineTo(width * 0.4, height * 0.35);
    path.lineTo(width * 0.5, height * 0.45);
    
    // Second diagonal
    path.lineTo(width * 0.6, height * 0.35);
    path.lineTo(width * 0.75, height * 0.65);
    
    // Right vertical line
    path.lineTo(width * 0.75, height * 0.2);
    path.lineTo(width * 0.9, height * 0.2);
    path.lineTo(width * 0.9, height * 0.8);
    path.lineTo(width * 0.75, height * 0.8);
    path.lineTo(width * 0.75, height * 0.75);
    
    // Close the M
    path.lineTo(width * 0.55, height * 0.55);
    path.lineTo(width * 0.5, height * 0.6);
    path.lineTo(width * 0.45, height * 0.55);
    path.lineTo(width * 0.25, height * 0.75);
    path.lineTo(width * 0.25, height * 0.8);
    path.close();

    canvas.drawPath(path, paint);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) {
    return false;
  }
}