@extends('layouts.dashboard')

@section('title', 'Upgrade Plan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Upgrade Your Plan</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Compare Plans</button>
                <button class="btn btn-primary btn-sm">Contact Sales</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Unlock more features and grow your business with our premium plans.
            </p>
        </div>
    </div>

    <!-- Current Plan -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Current Plan</h3>
        </div>
        <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
            <div style="width: 3rem; height: 3rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <span style="color: white; font-size: 1rem;">🚀</span>
            </div>
            <div style="flex: 1;">
                <div style="font-weight: 600; color: var(--text-primary); font-size: 1.125rem;">Starter Plan</div>
                <div style="color: var(--text-secondary); font-size: 0.875rem;">$29/month • Renews on January 15, 2024</div>
            </div>
            <div style="text-align: right;">
                <div style="font-weight: 600; color: var(--text-primary); font-size: 1.25rem;">$29</div>
                <div style="color: var(--text-secondary); font-size: 0.75rem;">per month</div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div style="text-align: center; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="font-weight: 600; color: var(--text-primary); font-size: 1.5rem;">5</div>
                <div style="color: var(--text-secondary); font-size: 0.875rem;">Bio Sites</div>
            </div>
            <div style="text-align: center; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="font-weight: 600; color: var(--text-primary); font-size: 1.5rem;">10K</div>
                <div style="color: var(--text-secondary); font-size: 0.875rem;">Monthly Visitors</div>
            </div>
            <div style="text-align: center; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="font-weight: 600; color: var(--text-primary); font-size: 1.5rem;">2</div>
                <div style="color: var(--text-secondary); font-size: 0.875rem;">Team Members</div>
            </div>
        </div>
    </div>

    <!-- Pricing Plans -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Pro Plan -->
        <div class="card">
            <div class="space-y-4">
                <div style="text-align: center; padding: 1rem;">
                    <div style="width: 4rem; height: 4rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <span style="color: white; font-size: 1.5rem;">⭐</span>
                    </div>
                    <h3 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Pro Plan</h3>
                    <div style="font-size: 2rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">$79</div>
                    <div style="color: var(--text-secondary); font-size: 0.875rem;">per month</div>
                </div>
                
                <div class="space-y-2">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--accent-secondary);">✅</span>
                        <span style="color: var(--text-primary); font-size: 0.875rem;">20 Bio Sites</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--accent-secondary);">✅</span>
                        <span style="color: var(--text-primary); font-size: 0.875rem;">100K Monthly Visitors</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--accent-secondary);">✅</span>
                        <span style="color: var(--text-primary); font-size: 0.875rem;">10 Team Members</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--accent-secondary);">✅</span>
                        <span style="color: var(--text-primary); font-size: 0.875rem;">Advanced Analytics</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--accent-secondary);">✅</span>
                        <span style="color: var(--text-primary); font-size: 0.875rem;">Email Marketing</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--accent-secondary);">✅</span>
                        <span style="color: var(--text-primary); font-size: 0.875rem;">Priority Support</span>
                    </div>
                </div>
                
                <button class="btn btn-primary w-full">Upgrade to Pro</button>
                <div style="text-align: center; color: var(--text-secondary); font-size: 0.75rem;">
                    Save $168 with annual billing
                </div>
            </div>
        </div>

        <!-- Business Plan -->
        <div class="card" style="border: 2px solid var(--accent-primary); position: relative;">
            <div style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: var(--accent-primary); color: white; padding: 0.25rem 1rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                MOST POPULAR
            </div>
            <div class="space-y-4">
                <div style="text-align: center; padding: 1rem;">
                    <div style="width: 4rem; height: 4rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <span style="color: white; font-size: 1.5rem;">🚀</span>
                    </div>
                    <h3 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Business Plan</h3>
                    <div style="font-size: 2rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">$149</div>
                    <div style="color: var(--text-secondary); font-size: 0.875rem;">per month</div>
                </div>
                
                <div class="space-y-2">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--accent-secondary);">✅</span>
                        <span style="color: var(--text-primary); font-size: 0.875rem;">Unlimited Bio Sites</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--accent-secondary);">✅</span>
                        <span style="color: var(--text-primary); font-size: 0.875rem;">500K Monthly Visitors</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--accent-secondary);">✅</span>
                        <span style="color: var(--text-primary); font-size: 0.875rem;">50 Team Members</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--accent-secondary);">✅</span>
                        <span style="color: var(--text-primary); font-size: 0.875rem;">AI-Powered Features</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--accent-secondary);">✅</span>
                        <span style="color: var(--text-primary); font-size: 0.875rem;">White Label Options</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--accent-secondary);">✅</span>
                        <span style="color: var(--text-primary); font-size: 0.875rem;">24/7 Premium Support</span>
                    </div>
                </div>
                
                <button class="btn btn-primary w-full">Upgrade to Business</button>
                <div style="text-align: center; color: var(--text-secondary); font-size: 0.75rem;">
                    Save $358 with annual billing
                </div>
            </div>
        </div>

        <!-- Enterprise Plan -->
        <div class="card">
            <div class="space-y-4">
                <div style="text-align: center; padding: 1rem;">
                    <div style="width: 4rem; height: 4rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <span style="color: white; font-size: 1.5rem;">👑</span>
                    </div>
                    <h3 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Enterprise</h3>
                    <div style="font-size: 2rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Custom</div>
                    <div style="color: var(--text-secondary); font-size: 0.875rem;">pricing</div>
                </div>
                
                <div class="space-y-2">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--accent-secondary);">✅</span>
                        <span style="color: var(--text-primary); font-size: 0.875rem;">Everything in Business</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--accent-secondary);">✅</span>
                        <span style="color: var(--text-primary); font-size: 0.875rem;">Unlimited Traffic</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--accent-secondary);">✅</span>
                        <span style="color: var(--text-primary); font-size: 0.875rem;">Unlimited Team Members</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--accent-secondary);">✅</span>
                        <span style="color: var(--text-primary); font-size: 0.875rem;">Custom Integrations</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--accent-secondary);">✅</span>
                        <span style="color: var(--text-primary); font-size: 0.875rem;">Dedicated Account Manager</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--accent-secondary);">✅</span>
                        <span style="color: var(--text-primary); font-size: 0.875rem;">SLA Guarantee</span>
                    </div>
                </div>
                
                <button class="btn btn-secondary w-full">Contact Sales</button>
                <div style="text-align: center; color: var(--text-secondary); font-size: 0.75rem;">
                    Custom pricing based on needs
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Comparison -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Feature Comparison</h3>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <th style="text-align: left; padding: 1rem; font-weight: 600; color: var(--text-primary);">Feature</th>
                        <th style="text-align: center; padding: 1rem; font-weight: 600; color: var(--text-primary);">Starter</th>
                        <th style="text-align: center; padding: 1rem; font-weight: 600; color: var(--text-primary);">Pro</th>
                        <th style="text-align: center; padding: 1rem; font-weight: 600; color: var(--text-primary);">Business</th>
                        <th style="text-align: center; padding: 1rem; font-weight: 600; color: var(--text-primary);">Enterprise</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">Bio Sites</td>
                        <td style="padding: 1rem; text-align: center; color: var(--text-primary);">5</td>
                        <td style="padding: 1rem; text-align: center; color: var(--text-primary);">20</td>
                        <td style="padding: 1rem; text-align: center; color: var(--text-primary);">Unlimited</td>
                        <td style="padding: 1rem; text-align: center; color: var(--text-primary);">Unlimited</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">Monthly Visitors</td>
                        <td style="padding: 1rem; text-align: center; color: var(--text-primary);">10K</td>
                        <td style="padding: 1rem; text-align: center; color: var(--text-primary);">100K</td>
                        <td style="padding: 1rem; text-align: center; color: var(--text-primary);">500K</td>
                        <td style="padding: 1rem; text-align: center; color: var(--text-primary);">Unlimited</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">Team Members</td>
                        <td style="padding: 1rem; text-align: center; color: var(--text-primary);">2</td>
                        <td style="padding: 1rem; text-align: center; color: var(--text-primary);">10</td>
                        <td style="padding: 1rem; text-align: center; color: var(--text-primary);">50</td>
                        <td style="padding: 1rem; text-align: center; color: var(--text-primary);">Unlimited</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">AI Features</td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--text-secondary);">❌</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--text-secondary);">❌</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">White Label</td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--text-secondary);">❌</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--text-secondary);">❌</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-primary);">
                        <td style="padding: 1rem; color: var(--text-primary);">Priority Support</td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--text-secondary);">❌</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                        <td style="padding: 1rem; text-align: center;"><span style="color: var(--accent-secondary);">✅</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upgrade Benefits -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Why Upgrade?</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div style="text-align: center; padding: 1rem;">
                <div style="width: 3rem; height: 3rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                    <span style="color: white; font-size: 1.25rem;">⚡</span>
                </div>
                <h4 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Increased Performance</h4>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">Handle more traffic and users with enhanced performance capabilities.</p>
            </div>
            
            <div style="text-align: center; padding: 1rem;">
                <div style="width: 3rem; height: 3rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                    <span style="color: white; font-size: 1.25rem;">🎯</span>
                </div>
                <h4 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Advanced Features</h4>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">Access powerful AI tools, analytics, and automation features.</p>
            </div>
            
            <div style="text-align: center; padding: 1rem;">
                <div style="width: 3rem; height: 3rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                    <span style="color: white; font-size: 1.25rem;">🛡️</span>
                </div>
                <h4 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Priority Support</h4>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">Get faster response times and dedicated support from our team.</p>
            </div>
        </div>
    </div>

    <!-- FAQ -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Upgrade FAQ</h3>
        </div>
        <div class="space-y-4">
            <div style="border: 1px solid var(--border-primary); border-radius: 8px; overflow: hidden;">
                <div style="padding: 1rem; background: var(--bg-primary); cursor: pointer; display: flex; justify-content: between; align-items: center;">
                    <div style="font-weight: 500; color: var(--text-primary);">When will I be charged after upgrading?</div>
                    <div style="color: var(--text-secondary);">+</div>
                </div>
            </div>
            
            <div style="border: 1px solid var(--border-primary); border-radius: 8px; overflow: hidden;">
                <div style="padding: 1rem; background: var(--bg-primary); cursor: pointer; display: flex; justify-content: between; align-items: center;">
                    <div style="font-weight: 500; color: var(--text-primary);">Can I downgrade my plan later?</div>
                    <div style="color: var(--text-secondary);">+</div>
                </div>
            </div>
            
            <div style="border: 1px solid var(--border-primary); border-radius: 8px; overflow: hidden;">
                <div style="padding: 1rem; background: var(--bg-primary); cursor: pointer; display: flex; justify-content: between; align-items: center;">
                    <div style="font-weight: 500; color: var(--text-primary);">What payment methods do you accept?</div>
                    <div style="color: var(--text-secondary);">+</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection