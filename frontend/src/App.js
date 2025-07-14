import React, { useState, useEffect } from 'react';
import axios from 'axios';

function App() {
  const [apiStatus, setApiStatus] = useState('loading');
  const [backendData, setBackendData] = useState(null);

  useEffect(() => {
    // Test API connection
    axios.get('/api/health')
      .then(response => {
        setApiStatus('connected');
        setBackendData(response.data);
      })
      .catch(error => {
        setApiStatus('error');
        console.error('API connection failed:', error);
      });
  }, []);

  return (
    <div style={{
      minHeight: '100vh',
      backgroundColor: '#101010',
      color: '#F1F1F1',
      padding: '2rem',
      fontFamily: 'Inter, sans-serif'
    }}>
      <div style={{
        maxWidth: '1200px',
        margin: '0 auto',
        textAlign: 'center'
      }}>
        <div style={{
          backgroundColor: '#191919',
          borderRadius: '12px',
          padding: '3rem',
          marginBottom: '2rem'
        }}>
          <h1 style={{
            fontSize: '3rem',
            fontWeight: '700',
            marginBottom: '1rem',
            background: 'linear-gradient(135deg, #FDFDFD, #7B7B7B)',
            WebkitBackgroundClip: 'text',
            WebkitTextFillColor: 'transparent'
          }}>
            Welcome to Mewayz
          </h1>
          <p style={{
            fontSize: '1.25rem',
            color: '#7B7B7B',
            marginBottom: '2rem'
          }}>
            Your all-in-one business platform for social media, e-commerce, courses, and more
          </p>
          
          <div style={{
            display: 'inline-flex',
            alignItems: 'center',
            backgroundColor: apiStatus === 'connected' ? '#26DE81' : apiStatus === 'error' ? '#FF3838' : '#F9CA24',
            color: '#141414',
            padding: '0.75rem 1.5rem',
            borderRadius: '8px',
            fontWeight: '600',
            marginBottom: '2rem'
          }}>
            <div style={{
              width: '8px',
              height: '8px',
              borderRadius: '50%',
              backgroundColor: '#141414',
              marginRight: '0.5rem'
            }}></div>
            API Status: {apiStatus === 'connected' ? 'Connected' : apiStatus === 'error' ? 'Error' : 'Loading...'}
          </div>
        </div>

        <div style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))',
          gap: '1.5rem',
          marginBottom: '3rem'
        }}>
          {[
            { title: 'Laravel Backend', description: 'API endpoints and business logic', color: '#FF6B6B' },
            { title: 'Flutter Mobile', description: 'Cross-platform mobile application', color: '#4ECDC4' },
            { title: 'React Web', description: 'Modern web interface', color: '#45B7D1' },
            { title: 'Database', description: 'MySQL data storage', color: '#F9CA24' }
          ].map((item, index) => (
            <div key={index} style={{
              backgroundColor: '#191919',
              borderRadius: '12px',
              padding: '2rem',
              border: `1px solid ${item.color}20`
            }}>
              <div style={{
                width: '48px',
                height: '48px',
                backgroundColor: item.color,
                borderRadius: '12px',
                margin: '0 auto 1rem'
              }}></div>
              <h3 style={{
                fontSize: '1.25rem',
                fontWeight: '600',
                marginBottom: '0.5rem',
                color: '#F1F1F1'
              }}>
                {item.title}
              </h3>
              <p style={{
                color: '#7B7B7B',
                fontSize: '0.9rem'
              }}>
                {item.description}
              </p>
            </div>
          ))}
        </div>

        {backendData && (
          <div style={{
            backgroundColor: '#191919',
            borderRadius: '12px',
            padding: '2rem',
            textAlign: 'left'
          }}>
            <h3 style={{
              fontSize: '1.25rem',
              fontWeight: '600',
              marginBottom: '1rem',
              color: '#F1F1F1'
            }}>
              System Status
            </h3>
            <pre style={{
              backgroundColor: '#101010',
              padding: '1rem',
              borderRadius: '8px',
              overflow: 'auto',
              fontSize: '0.875rem',
              color: '#7B7B7B'
            }}>
              {JSON.stringify(backendData, null, 2)}
            </pre>
          </div>
        )}

        <div style={{
          marginTop: '3rem',
          padding: '2rem',
          backgroundColor: '#191919',
          borderRadius: '12px'
        }}>
          <h2 style={{
            fontSize: '1.5rem',
            fontWeight: '600',
            marginBottom: '1rem'
          }}>
            Next Steps
          </h2>
          <div style={{
            textAlign: 'left',
            color: '#7B7B7B'
          }}>
            <p>• Laravel backend is configured with comprehensive API routes</p>
            <p>• Database migrations need to be run</p>
            <p>• Flutter mobile app is ready for development</p>
            <p>• Third-party integrations (Stripe, ElasticEmail) are configured</p>
            <p>• Professional design system implementation in progress</p>
          </div>
        </div>
      </div>
    </div>
  );
}

export default App;