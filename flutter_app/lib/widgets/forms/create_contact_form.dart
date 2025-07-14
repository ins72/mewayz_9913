import 'package:flutter/material.dart';
import '../../config/colors.dart';

class CreateContactForm extends StatefulWidget {
  const CreateContactForm({super.key});

  @override
  State<CreateContactForm> createState() => _CreateContactFormState();
}

class _CreateContactFormState extends State<CreateContactForm> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _companyController = TextEditingController();
  final _notesController = TextEditingController();
  String _selectedType = 'Lead';
  String _selectedStatus = 'Cold';

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _companyController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 500,
      padding: const EdgeInsets.all(24),
      child: Form(
        key: _formKey,
        child: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Add New Contact',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 24),
              
              // Name field
              TextFormField(
                controller: _nameController,
                decoration: const InputDecoration(
                  labelText: 'Full Name',
                  hintText: 'Enter contact name',
                  border: OutlineInputBorder(),
                  labelStyle: TextStyle(color: AppColors.textSecondary),
                  hintStyle: TextStyle(color: AppColors.textSecondary),
                ),
                style: const TextStyle(color: AppColors.textPrimary),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter a name';
                  }
                  return null;
                },
              ),
              
              const SizedBox(height: 16),
              
              // Email field
              TextFormField(
                controller: _emailController,
                decoration: const InputDecoration(
                  labelText: 'Email',
                  hintText: 'Enter email address',
                  border: OutlineInputBorder(),
                  labelStyle: TextStyle(color: AppColors.textSecondary),
                  hintStyle: TextStyle(color: AppColors.textSecondary),
                ),
                style: const TextStyle(color: AppColors.textPrimary),
                keyboardType: TextInputType.emailAddress,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter an email';
                  }
                  if (!RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$').hasMatch(value)) {
                    return 'Please enter a valid email';
                  }
                  return null;
                },
              ),
              
              const SizedBox(height: 16),
              
              // Phone field
              TextFormField(
                controller: _phoneController,
                decoration: const InputDecoration(
                  labelText: 'Phone',
                  hintText: 'Enter phone number',
                  border: OutlineInputBorder(),
                  labelStyle: TextStyle(color: AppColors.textSecondary),
                  hintStyle: TextStyle(color: AppColors.textSecondary),
                ),
                style: const TextStyle(color: AppColors.textPrimary),
                keyboardType: TextInputType.phone,
              ),
              
              const SizedBox(height: 16),
              
              // Company field
              TextFormField(
                controller: _companyController,
                decoration: const InputDecoration(
                  labelText: 'Company',
                  hintText: 'Enter company name',
                  border: OutlineInputBorder(),
                  labelStyle: TextStyle(color: AppColors.textSecondary),
                  hintStyle: TextStyle(color: AppColors.textSecondary),
                ),
                style: const TextStyle(color: AppColors.textPrimary),
              ),
              
              const SizedBox(height: 16),
              
              // Type and Status dropdowns
              Row(
                children: [
                  Expanded(
                    child: DropdownButtonFormField<String>(
                      value: _selectedType,
                      decoration: const InputDecoration(
                        labelText: 'Type',
                        border: OutlineInputBorder(),
                        labelStyle: TextStyle(color: AppColors.textSecondary),
                      ),
                      style: const TextStyle(color: AppColors.textPrimary),
                      dropdownColor: AppColors.surface,
                      items: ['Lead', 'Prospect', 'Customer', 'Partner']
                          .map((type) => DropdownMenuItem(
                                value: type,
                                child: Text(type),
                              ))
                          .toList(),
                      onChanged: (value) {
                        setState(() {
                          _selectedType = value!;
                        });
                      },
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: DropdownButtonFormField<String>(
                      value: _selectedStatus,
                      decoration: const InputDecoration(
                        labelText: 'Status',
                        border: OutlineInputBorder(),
                        labelStyle: TextStyle(color: AppColors.textSecondary),
                      ),
                      style: const TextStyle(color: AppColors.textPrimary),
                      dropdownColor: AppColors.surface,
                      items: ['Cold', 'Warm', 'Hot', 'Active', 'Inactive']
                          .map((status) => DropdownMenuItem(
                                value: status,
                                child: Text(status),
                              ))
                          .toList(),
                      onChanged: (value) {
                        setState(() {
                          _selectedStatus = value!;
                        });
                      },
                    ),
                  ),
                ],
              ),
              
              const SizedBox(height: 16),
              
              // Notes field
              TextFormField(
                controller: _notesController,
                maxLines: 3,
                decoration: const InputDecoration(
                  labelText: 'Notes',
                  hintText: 'Additional information...',
                  border: OutlineInputBorder(),
                  labelStyle: TextStyle(color: AppColors.textSecondary),
                  hintStyle: TextStyle(color: AppColors.textSecondary),
                ),
                style: const TextStyle(color: AppColors.textPrimary),
              ),
              
              const SizedBox(height: 24),
              
              // Action buttons
              Row(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  TextButton(
                    onPressed: () => Navigator.pop(context),
                    child: const Text(
                      'Cancel',
                      style: TextStyle(color: AppColors.textSecondary),
                    ),
                  ),
                  const SizedBox(width: 16),
                  ElevatedButton(
                    onPressed: _createContact,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.primary,
                      foregroundColor: Colors.white,
                    ),
                    child: const Text('Add Contact'),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _createContact() {
    if (_formKey.currentState!.validate()) {
      // TODO: Implement contact creation
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Contact added successfully!'),
          backgroundColor: Color(0xFF26DE81),
        ),
      );
    }
  }
}