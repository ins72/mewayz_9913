#!/usr/bin/env python3
"""
Stripe Payment Integration for Laravel Backend
Handles Stripe checkout sessions and webhooks using official Stripe library
"""

import json
import os
import sys
from typing import Dict, Any, Optional
import stripe

class StripeIntegration:
    def __init__(self, api_key: str, webhook_url: str):
        stripe.api_key = api_key
        self.webhook_url = webhook_url
        self.webhook_secret = os.getenv('STRIPE_WEBHOOK_SECRET', '')
        
    def create_checkout_session(self, request_data: Dict[str, Any]) -> Dict[str, Any]:
        """Create a Stripe checkout session"""
        try:
            # Extract request data
            amount = request_data.get('amount')
            currency = request_data.get('currency', 'USD').lower()
            stripe_price_id = request_data.get('stripe_price_id')
            quantity = request_data.get('quantity', 1)
            success_url = request_data.get('success_url')
            cancel_url = request_data.get('cancel_url')
            metadata = request_data.get('metadata', {})
            
            # Create checkout session parameters
            session_params = {
                'success_url': success_url,
                'cancel_url': cancel_url,
                'mode': 'payment',
                'metadata': metadata
            }
            
            if stripe_price_id:
                # Fixed price product
                session_params['line_items'] = [{
                    'price': stripe_price_id,
                    'quantity': quantity,
                }]
            else:
                # Custom amount - create a price on the fly
                session_params['line_items'] = [{
                    'price_data': {
                        'currency': currency,
                        'product_data': {
                            'name': 'Payment',
                        },
                        'unit_amount': int(float(amount) * 100),  # Convert to cents
                    },
                    'quantity': 1,
                }]
            
            # Create session
            session = stripe.checkout.Session.create(**session_params)
            
            return {
                'success': True,
                'url': session.url,
                'session_id': session.id
            }
            
        except Exception as e:
            return {
                'success': False,
                'error': str(e)
            }
    
    def get_checkout_status(self, session_id: str) -> Dict[str, Any]:
        """Get the status of a checkout session"""
        try:
            session = stripe.checkout.Session.retrieve(session_id)
            
            # Map Stripe status to our format
            payment_status = 'unpaid'
            if session.payment_status == 'paid':
                payment_status = 'paid'
            elif session.payment_status == 'unpaid':
                payment_status = 'unpaid'
            elif session.payment_status == 'no_payment_required':
                payment_status = 'paid'
            
            return {
                'success': True,
                'status': session.status,
                'payment_status': payment_status,
                'amount_total': session.amount_total,
                'currency': session.currency,
                'metadata': session.metadata or {}
            }
            
        except Exception as e:
            return {
                'success': False,
                'error': str(e)
            }
    
    def handle_webhook(self, request_body: bytes, signature: str) -> Dict[str, Any]:
        """Handle Stripe webhook"""
        try:
            if not self.webhook_secret:
                # If no webhook secret, just parse the event
                event = json.loads(request_body.decode('utf-8'))
            else:
                # Verify webhook signature
                event = stripe.Webhook.construct_event(
                    request_body, signature, self.webhook_secret
                )
            
            # Extract relevant information
            event_type = event['type']
            event_id = event['id']
            
            session_id = ''
            payment_status = 'unknown'
            metadata = {}
            
            if event_type == 'checkout.session.completed':
                session = event['data']['object']
                session_id = session['id']
                payment_status = 'paid' if session['payment_status'] == 'paid' else 'unpaid'
                metadata = session.get('metadata', {})
            
            return {
                'success': True,
                'event_type': event_type,
                'event_id': event_id,
                'session_id': session_id,
                'payment_status': payment_status,
                'metadata': metadata
            }
            
        except Exception as e:
            return {
                'success': False,
                'error': str(e)
            }


def main():
    """Main function to handle command line requests"""
    if len(sys.argv) < 2:
        print(json.dumps({'success': False, 'error': 'No command provided'}))
        return
    
    command = sys.argv[1]
    
    # Get API key from environment
    api_key = os.getenv('STRIPE_API_KEY')
    if not api_key:
        print(json.dumps({'success': False, 'error': 'STRIPE_API_KEY not found'}))
        return
    
    # Get webhook URL from command line args
    webhook_url = sys.argv[2] if len(sys.argv) > 2 else ''
    
    stripe_integration = StripeIntegration(api_key, webhook_url)
    
    if command == 'create_session':
        # Read request data from stdin
        request_data = json.loads(sys.stdin.read())
        result = stripe_integration.create_checkout_session(request_data)
        print(json.dumps(result))
        
    elif command == 'get_status':
        session_id = sys.argv[2]
        result = stripe_integration.get_checkout_status(session_id)
        print(json.dumps(result))
        
    elif command == 'handle_webhook':
        # Read webhook data from stdin
        webhook_data = json.loads(sys.stdin.read())
        request_body = webhook_data['body'].encode()
        signature = webhook_data['signature']
        result = stripe_integration.handle_webhook(request_body, signature)
        print(json.dumps(result))
        
    else:
        print(json.dumps({'success': False, 'error': f'Unknown command: {command}'}))


if __name__ == '__main__':
    main()