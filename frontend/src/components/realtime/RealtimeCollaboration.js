import React, { useState, useEffect, useRef, useContext } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { 
  UserGroupIcon,
  CursorArrowRaysIcon,
  ChatBubbleLeftRightIcon,
  VideoCameraIcon,
  MicrophoneIcon,
  ShareIcon,
  HandRaisedIcon,
  DocumentTextIcon,
  PencilSquareIcon
} from '@heroicons/react/24/outline';
import { AuthContext } from '../../contexts/AuthContext';

const RealtimeCollaboration = ({ documentId, documentType = 'general' }) => {
  const { user } = useContext(AuthContext);
  const [isConnected, setIsConnected] = useState(false);
  const [messages, setMessages] = useState([]);
  const [newMessage, setNewMessage] = useState('');
  const [cursors, setCursors] = useState({});
  const [isVideoCall, setIsVideoCall] = useState(false);
  const [isVoiceCall, setIsVoiceCall] = useState(false);
  const [activeUsers, setActiveUsers] = useState([]);
  const wsRef = useRef(null);
  const messagesEndRef = useRef(null);

  // Mock WebSocket connection with enhanced features
  useEffect(() => {
    const mockConnect = () => {
      setIsConnected(true);
      
      // Simulate other users joining
      setTimeout(() => {
        setActiveUsers([
          {
            id: '1',
            name: 'Sarah Johnson',
            avatar: 'https://ui-avatars.io/api/?name=Sarah+Johnson&background=3b82f6&color=white',
            status: 'online',
            cursor: { x: 150, y: 200 },
            color: '#3b82f6'
          },
          {
            id: '2', 
            name: 'Mike Chen',
            avatar: 'https://ui-avatars.io/api/?name=Mike+Chen&background=10b981&color=white',
            status: 'editing',
            cursor: { x: 300, y: 150 },
            color: '#10b981'
          }
        ]);
      }, 1000);

      // Simulate live messages
      setTimeout(() => {
        setMessages([
          {
            id: '1',
            user: 'Sarah Johnson',
            message: 'Started working on the realtime collaboration features',
            timestamp: new Date().toISOString(),
            type: 'activity'
          },
          {
            id: '2',
            user: 'Mike Chen', 
            message: 'This WebSocket integration looks great! Real-time updates working perfectly.',
            timestamp: new Date().toISOString(),
            type: 'message'
          }
        ]);
      }, 2000);
    };

    mockConnect();

    return () => {
      if (wsRef.current) {
        wsRef.current.close();
      }
    };
  }, [documentId]);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: "smooth" });
  };

  useEffect(scrollToBottom, [messages]);

  const handleSendMessage = (e) => {
    e.preventDefault();
    if (!newMessage.trim()) return;

    const message = {
      id: Date.now().toString(),
      user: user?.name || 'You',
      message: newMessage,
      timestamp: new Date().toISOString(),
      type: 'message'
    };

    setMessages(prev => [...prev, message]);
    setNewMessage('');
  };

  const startVideoCall = () => {
    setIsVideoCall(!isVideoCall);
  };

  const startVoiceCall = () => {
    setIsVoiceCall(!isVoiceCall);
  };

  const shareCursor = (e) => {
    const rect = e.currentTarget.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    setCursors(prev => ({
      ...prev,
      [user?.id]: { x, y, user: user?.name, color: '#f59e0b' }
    }));
  };

  return (
    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
      {/* Collaboration Header */}
      <div className="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <div className="flex items-center space-x-3">
          <div className={`flex items-center space-x-2 ${isConnected ? 'text-green-600' : 'text-gray-400'}`}>
            <div className={`w-2 h-2 rounded-full ${isConnected ? 'bg-green-500' : 'bg-gray-400'} animate-pulse`}></div>
            <span className="text-sm font-medium">
              {isConnected ? 'Live Collaboration' : 'Connecting...'}
            </span>
          </div>
          
          {/* Active Users */}
          <div className="flex -space-x-2">
            <img
              src={`https://ui-avatars.io/api/?name=${encodeURIComponent(user?.name || 'You')}&background=f59e0b&color=white`}
              alt="You"
              className="w-8 h-8 rounded-full border-2 border-white dark:border-gray-700"
              title={`You (${user?.name})`}
            />
            {activeUsers.map((collaborator) => (
              <img
                key={collaborator.id}
                src={collaborator.avatar}
                alt={collaborator.name}
                className="w-8 h-8 rounded-full border-2 border-white dark:border-gray-700"
                title={`${collaborator.name} (${collaborator.status})`}
              />
            ))}
          </div>
          
          <span className="text-xs text-gray-500 dark:text-gray-400">
            {activeUsers.length + 1} online
          </span>
        </div>

        {/* Collaboration Tools */}
        <div className="flex items-center space-x-2">
          <button
            onClick={startVoiceCall}
            className={`p-2 rounded-lg transition-colors ${
              isVoiceCall 
                ? 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400' 
                : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700'
            }`}
            title="Voice Call"
          >
            <MicrophoneIcon className="h-5 w-5" />
          </button>
          
          <button
            onClick={startVideoCall}
            className={`p-2 rounded-lg transition-colors ${
              isVideoCall 
                ? 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400' 
                : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700'
            }`}
            title="Video Call"
          >
            <VideoCameraIcon className="h-5 w-5" />
          </button>
          
          <button
            className="p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
            title="Share Screen"
          >
            <ShareIcon className="h-5 w-5" />
          </button>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-4 h-96">
        {/* Main Collaboration Area */}
        <div className="lg:col-span-3 relative" onMouseMove={shareCursor}>
          <div className="p-4 h-full">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Collaborative Workspace
            </h3>
            
            {/* Document Preview/Editor Area */}
            <div className="relative bg-gray-50 dark:bg-gray-900 rounded-lg p-6 h-64 overflow-auto">
              <div className="prose dark:prose-invert max-w-none">
                <h4 className="text-base font-medium mb-3">Document: {documentType}</h4>
                <div className="bg-white dark:bg-gray-800 border rounded-lg p-4 mb-4">
                  <textarea
                    className="w-full h-32 bg-transparent border-none resize-none focus:outline-none text-gray-900 dark:text-white"
                    placeholder="Start typing to collaborate in real-time..."
                    defaultValue="This is a collaborative workspace where team members can work together in real-time. Changes are synchronized instantly across all connected users.

Click here to start editing and see real-time collaboration features in action!"
                  />
                </div>
                <div className="mt-4 space-y-2">
                  <div className="p-2 bg-blue-50 dark:bg-blue-900/20 rounded border-l-4 border-blue-500">
                    <p className="text-sm text-blue-800 dark:text-blue-200">
                      <strong>Sarah Johnson</strong> is currently editing this section...
                    </p>
                  </div>
                  <div className="p-2 bg-green-50 dark:bg-green-900/20 rounded border-l-4 border-green-500">
                    <p className="text-sm text-green-800 dark:text-green-200">
                      <strong>Mike Chen</strong> added comments on the WebSocket implementation
                    </p>
                  </div>
                </div>
              </div>

              {/* Live Cursors */}
              {Object.entries(cursors).map(([userId, cursor]) => (
                <motion.div
                  key={userId}
                  className="absolute pointer-events-none z-10"
                  style={{ 
                    left: cursor.x, 
                    top: cursor.y, 
                    color: cursor.color 
                  }}
                  initial={{ scale: 0 }}
                  animate={{ scale: 1 }}
                  exit={{ scale: 0 }}
                >
                  <CursorArrowRaysIcon className="h-5 w-5" style={{ color: cursor.color }} />
                  <div 
                    className="text-xs font-medium px-2 py-1 rounded shadow-lg text-white mt-1"
                    style={{ backgroundColor: cursor.color }}
                  >
                    {cursor.user}
                  </div>
                </motion.div>
              ))}
            </div>

            {/* Quick Actions */}
            <div className="flex items-center justify-between mt-4">
              <div className="flex items-center space-x-3">
                <button className="flex items-center space-x-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                  <PencilSquareIcon className="h-4 w-4" />
                  <span>Start Editing</span>
                </button>
                <button className="flex items-center space-x-2 px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm">
                  <HandRaisedIcon className="h-4 w-4" />
                  <span>Raise Hand</span>
                </button>
              </div>
              
              <div className="text-xs text-gray-500 dark:text-gray-400">
                Auto-saved just now
              </div>
            </div>
          </div>
        </div>

        {/* Chat Panel */}
        <div className="lg:col-span-1 border-l border-gray-200 dark:border-gray-700 flex flex-col">
          <div className="p-4 border-b border-gray-200 dark:border-gray-700">
            <h4 className="flex items-center text-sm font-medium text-gray-900 dark:text-white">
              <ChatBubbleLeftRightIcon className="h-4 w-4 mr-2" />
              Team Chat
            </h4>
          </div>

          {/* Messages */}
          <div className="flex-1 overflow-y-auto p-4 space-y-3">
            <AnimatePresence>
              {messages.map((message) => (
                <motion.div
                  key={message.id}
                  initial={{ opacity: 0, y: 10 }}
                  animate={{ opacity: 1, y: 0 }}
                  exit={{ opacity: 0, y: -10 }}
                  className={`${
                    message.type === 'activity' 
                      ? 'text-center text-xs text-gray-500 italic' 
                      : ''
                  }`}
                >
                  {message.type === 'activity' ? (
                    <div className="flex items-center justify-center space-x-2">
                      <DocumentTextIcon className="h-3 w-3" />
                      <span>{message.user} {message.message}</span>
                    </div>
                  ) : (
                    <div className="space-y-1">
                      <div className="flex items-center space-x-2">
                        <span className="text-xs font-medium text-gray-900 dark:text-white">
                          {message.user}
                        </span>
                        <span className="text-xs text-gray-400">
                          {new Date(message.timestamp).toLocaleTimeString()}
                        </span>
                      </div>
                      <p className="text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-900 rounded-lg p-2">
                        {message.message}
                      </p>
                    </div>
                  )}
                </motion.div>
              ))}
            </AnimatePresence>
            <div ref={messagesEndRef} />
          </div>

          {/* Message Input */}
          <div className="p-4 border-t border-gray-200 dark:border-gray-700">
            <form onSubmit={handleSendMessage} className="flex space-x-2">
              <input
                type="text"
                value={newMessage}
                onChange={(e) => setNewMessage(e.target.value)}
                placeholder="Type a message..."
                className="flex-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <button
                type="submit"
                disabled={!newMessage.trim()}
                className="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <ShareIcon className="h-4 w-4" />
              </button>
            </form>
          </div>
        </div>
      </div>

      {/* Video Call Overlay */}
      <AnimatePresence>
        {isVideoCall && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
          >
            <div className="bg-gray-900 rounded-lg p-6 max-w-4xl w-full mx-4">
              <div className="flex justify-between items-center mb-4">
                <h3 className="text-lg font-semibold text-white">Team Video Call</h3>
                <button
                  onClick={() => setIsVideoCall(false)}
                  className="text-gray-400 hover:text-white"
                >
                  ✕
                </button>
              </div>
              
              <div className="grid grid-cols-2 gap-4 mb-4">
                <div className="bg-gray-800 rounded-lg aspect-video flex items-center justify-center">
                  <div className="text-center text-white">
                    <VideoCameraIcon className="h-12 w-12 mx-auto mb-2" />
                    <p>You</p>
                  </div>
                </div>
                <div className="bg-gray-800 rounded-lg aspect-video flex items-center justify-center">
                  <div className="text-center text-white">
                    <VideoCameraIcon className="h-12 w-12 mx-auto mb-2" />
                    <p>Sarah Johnson</p>
                  </div>
                </div>
              </div>
              
              <div className="flex justify-center space-x-4">
                <button className="p-3 bg-red-600 text-white rounded-full hover:bg-red-700">
                  <VideoCameraIcon className="h-5 w-5" />
                </button>
                <button className="p-3 bg-gray-600 text-white rounded-full hover:bg-gray-700">
                  <MicrophoneIcon className="h-5 w-5" />
                </button>
                <button className="p-3 bg-gray-600 text-white rounded-full hover:bg-gray-700">
                  <ShareIcon className="h-5 w-5" />
                </button>
              </div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
};

export default RealtimeCollaboration;

  return (
    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
      {/* Collaboration Header */}
      <div className="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <div className="flex items-center space-x-3">
          <div className={`flex items-center space-x-2 ${isConnected ? 'text-green-600' : 'text-gray-400'}`}>
            <div className={`w-2 h-2 rounded-full ${isConnected ? 'bg-green-500' : 'bg-gray-400'} animate-pulse`}></div>
            <span className="text-sm font-medium">
              {isConnected ? 'Live Collaboration' : 'Connecting...'}
            </span>
          </div>
          
          {/* Active Users */}
          <div className="flex -space-x-2">
            <img
              src={`https://ui-avatars.io/api/?name=${encodeURIComponent(user?.name || 'You')}&background=f59e0b&color=white`}
              alt="You"
              className="w-8 h-8 rounded-full border-2 border-white dark:border-gray-700"
              title={`You (${user?.name})`}
            />
            {activeUsers.map((collaborator) => (
              <div key={collaborator.id} className="relative">
                <img
                  src={collaborator.avatar}
                  alt={collaborator.name}
                  className="w-8 h-8 rounded-full border-2 border-white dark:border-gray-700"
                  title={`${collaborator.name} (${collaborator.status})`}
                />
                {isTyping[collaborator.id] && (
                  <div className="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                )}
              </div>
            ))}
          </div>
          
          <span className="text-xs text-gray-500 dark:text-gray-400">
            {activeUsers.length + 1} online
          </span>
        </div>

        {/* Collaboration Tools */}
        <div className="flex items-center space-x-2">
          <button
            onClick={startVoiceCall}
            className={`p-2 rounded-lg transition-colors ${
              isVoiceCall 
                ? 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400' 
                : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700'
            }`}
            title="Voice Call"
          >
            <MicrophoneIcon className="h-5 w-5" />
          </button>
          
          <button
            onClick={startVideoCall}
            className={`p-2 rounded-lg transition-colors ${
              isVideoCall 
                ? 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400' 
                : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700'
            }`}
            title="Video Call"
          >
            <VideoCameraIcon className="h-5 w-5" />
          </button>
          
          <button
            className="p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
            title="Share Screen"
          >
            <ShareIcon className="h-5 w-5" />
          </button>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-4 h-96">
        {/* Main Collaboration Area */}
        <div className="lg:col-span-3 relative" onMouseMove={shareCursor}>
          <div className="p-4 h-full">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Collaborative Workspace
            </h3>
            
            {/* Document Editor Area */}
            <div className="relative bg-gray-50 dark:bg-gray-900 rounded-lg p-6 h-64 overflow-auto">
              <textarea
                value={documentContent}
                onChange={(e) => handleDocumentChange(e.target.value)}
                onFocus={handleTypingStart}
                onBlur={handleTypingStop}
                className="w-full h-full bg-transparent border-none resize-none focus:outline-none text-gray-900 dark:text-white"
                placeholder="Start typing to collaborate in real-time..."
              />

              {/* Live Cursors */}
              {Object.entries(cursors).map(([userId, cursor]) => (
                <motion.div
                  key={userId}
                  className="absolute pointer-events-none z-10"
                  style={{ 
                    left: cursor.x, 
                    top: cursor.y, 
                    color: cursor.color 
                  }}
                  initial={{ scale: 0 }}
                  animate={{ scale: 1 }}
                  exit={{ scale: 0 }}
                >
                  <CursorArrowRaysIcon className="h-5 w-5" style={{ color: cursor.color }} />
                  <div 
                    className="text-xs font-medium px-2 py-1 rounded shadow-lg text-white mt-1"
                    style={{ backgroundColor: cursor.color }}
                  >
                    {cursor.user}
                  </div>
                </motion.div>
              ))}

              {/* Collaboration Status */}
              <div className="absolute bottom-2 left-2">
                {Object.values(isTyping).some(Boolean) && (
                  <div className="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                    <div className="flex space-x-1">
                      <div className="w-1 h-1 bg-gray-400 rounded-full animate-pulse"></div>
                      <div className="w-1 h-1 bg-gray-400 rounded-full animate-pulse" style={{ animationDelay: '0.2s' }}></div>
                      <div className="w-1 h-1 bg-gray-400 rounded-full animate-pulse" style={{ animationDelay: '0.4s' }}></div>
                    </div>
                    <span>Someone is typing...</span>
                  </div>
                )}
              </div>
            </div>

            {/* Quick Actions */}
            <div className="flex items-center justify-between mt-4">
              <div className="flex items-center space-x-3">
                <button className="flex items-center space-x-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                  <PencilSquareIcon className="h-4 w-4" />
                  <span>Editing</span>
                </button>
                <button className="flex items-center space-x-2 px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm">
                  <HandRaisedIcon className="h-4 w-4" />
                  <span>Raise Hand</span>
                </button>
              </div>
              
              <div className="text-xs text-gray-500 dark:text-gray-400">
                Auto-saved just now
              </div>
            </div>
          </div>
        </div>

        {/* Chat Panel */}
        <div className="lg:col-span-1 border-l border-gray-200 dark:border-gray-700 flex flex-col">
          <div className="p-4 border-b border-gray-200 dark:border-gray-700">
            <h4 className="flex items-center text-sm font-medium text-gray-900 dark:text-white">
              <ChatBubbleLeftRightIcon className="h-4 w-4 mr-2" />
              Team Chat
            </h4>
          </div>

          {/* Messages */}
          <div className="flex-1 overflow-y-auto p-4 space-y-3">
            <AnimatePresence>
              {messages.map((message) => (
                <motion.div
                  key={message.id}
                  initial={{ opacity: 0, y: 10 }}
                  animate={{ opacity: 1, y: 0 }}
                  exit={{ opacity: 0, y: -10 }}
                  className={`${
                    message.type === 'activity' 
                      ? 'text-center text-xs text-gray-500 italic' 
                      : ''
                  }`}
                >
                  {message.type === 'activity' ? (
                    <div className="flex items-center justify-center space-x-2">
                      <DocumentTextIcon className="h-3 w-3" />
                      <span>{message.user} {message.message}</span>
                    </div>
                  ) : (
                    <div className="space-y-1">
                      <div className="flex items-center space-x-2">
                        <span className="text-xs font-medium text-gray-900 dark:text-white">
                          {message.user}
                        </span>
                        <span className="text-xs text-gray-400">
                          {new Date(message.timestamp).toLocaleTimeString()}
                        </span>
                      </div>
                      <p className="text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-900 rounded-lg p-2">
                        {message.message}
                      </p>
                    </div>
                  )}
                </motion.div>
              ))}
            </AnimatePresence>
            <div ref={messagesEndRef} />
          </div>

          {/* Message Input */}
          <div className="p-4 border-t border-gray-200 dark:border-gray-700">
            <form onSubmit={handleSendMessage} className="flex space-x-2">
              <input
                type="text"
                value={newMessage}
                onChange={(e) => setNewMessage(e.target.value)}
                placeholder="Type a message..."
                className="flex-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <button
                type="submit"
                disabled={!newMessage.trim()}
                className="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <ShareIcon className="h-4 w-4" />
              </button>
            </form>
          </div>
        </div>
      </div>

      {/* Video Call Overlay */}
      <AnimatePresence>
        {isVideoCall && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
          >
            <div className="bg-gray-900 rounded-lg p-6 max-w-4xl w-full mx-4">
              <div className="flex justify-between items-center mb-4">
                <h3 className="text-lg font-semibold text-white">Team Video Call</h3>
                <button
                  onClick={() => setIsVideoCall(false)}
                  className="text-gray-400 hover:text-white"
                >
                  ✕
                </button>
              </div>
              
              <div className="grid grid-cols-2 gap-4 mb-4">
                <div className="bg-gray-800 rounded-lg aspect-video flex items-center justify-center">
                  <div className="text-center text-white">
                    <VideoCameraIcon className="h-12 w-12 mx-auto mb-2" />
                    <p>You</p>
                  </div>
                </div>
                <div className="bg-gray-800 rounded-lg aspect-video flex items-center justify-center">
                  <div className="text-center text-white">
                    <VideoCameraIcon className="h-12 w-12 mx-auto mb-2" />
                    <p>Sarah Johnson</p>
                  </div>
                </div>
              </div>
              
              <div className="flex justify-center space-x-4">
                <button className="p-3 bg-red-600 text-white rounded-full hover:bg-red-700">
                  <VideoCameraIcon className="h-5 w-5" />
                </button>
                <button className="p-3 bg-gray-600 text-white rounded-full hover:bg-gray-700">
                  <MicrophoneIcon className="h-5 w-5" />
                </button>
                <button className="p-3 bg-gray-600 text-white rounded-full hover:bg-gray-700">
                  <ShareIcon className="h-5 w-5" />
                </button>
              </div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
};

export default RealtimeCollaboration;

  return (
    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
      {/* Collaboration Header */}
      <div className="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <div className="flex items-center space-x-3">
          <div className={`flex items-center space-x-2 ${isConnected ? 'text-green-600' : 'text-gray-400'}`}>
            <div className={`w-2 h-2 rounded-full ${isConnected ? 'bg-green-500' : 'bg-gray-400'} animate-pulse`}></div>
            <span className="text-sm font-medium">
              {isConnected ? 'Live Collaboration' : 'Connecting...'}
            </span>
          </div>
          
          {/* Active Users */}
          <div className="flex -space-x-2">
            <img
              src={`https://ui-avatars.io/api/?name=${encodeURIComponent(user?.name || 'You')}&background=f59e0b&color=white`}
              alt="You"
              className="w-8 h-8 rounded-full border-2 border-white dark:border-gray-700"
              title={`You (${user?.name})`}
            />
            {activeUsers.map((collaborator) => (
              <div key={collaborator.id} className="relative">
                <img
                  src={collaborator.avatar}
                  alt={collaborator.name}
                  className="w-8 h-8 rounded-full border-2 border-white dark:border-gray-700"
                  title={`${collaborator.name} (${collaborator.status})`}
                />
                {isTyping[collaborator.id] && (
                  <div className="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                )}
              </div>
            ))}
          </div>
          
          <span className="text-xs text-gray-500 dark:text-gray-400">
            {activeUsers.length + 1} online
          </span>
        </div>

        {/* Collaboration Tools */}
        <div className="flex items-center space-x-2">
          <button
            onClick={startVoiceCall}
            className={`p-2 rounded-lg transition-colors ${
              isVoiceCall 
                ? 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400' 
                : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700'
            }`}
            title="Voice Call"
          >
            <MicrophoneIcon className="h-5 w-5" />
          </button>
          
          <button
            onClick={startVideoCall}
            className={`p-2 rounded-lg transition-colors ${
              isVideoCall 
                ? 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400' 
                : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700'
            }`}
            title="Video Call"
          >
            <VideoCameraIcon className="h-5 w-5" />
          </button>
          
          <button
            className="p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
            title="Share Screen"
          >
            <ShareIcon className="h-5 w-5" />
          </button>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-4 h-96">
        {/* Main Collaboration Area */}
        <div className="lg:col-span-3 relative" onMouseMove={shareCursor}>
          <div className="p-4 h-full">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Collaborative Workspace
            </h3>
            
            {/* Document Editor Area */}
            <div className="relative bg-gray-50 dark:bg-gray-900 rounded-lg p-6 h-64 overflow-auto">
              <textarea
                value={documentContent}
                onChange={(e) => handleDocumentChange(e.target.value)}
                onFocus={handleTypingStart}
                onBlur={handleTypingStop}
                className="w-full h-full bg-transparent border-none resize-none focus:outline-none text-gray-900 dark:text-white"
                placeholder="Start typing to collaborate in real-time..."
              />

              {/* Live Cursors */}
              {Object.entries(cursors).map(([userId, cursor]) => (
                <motion.div
                  key={userId}
                  className="absolute pointer-events-none z-10"
                  style={{ 
                    left: cursor.x, 
                    top: cursor.y, 
                    color: cursor.color 
                  }}
                  initial={{ scale: 0 }}
                  animate={{ scale: 1 }}
                  exit={{ scale: 0 }}
                >
                  <CursorArrowRaysIcon className="h-5 w-5" style={{ color: cursor.color }} />
                  <div 
                    className="text-xs font-medium px-2 py-1 rounded shadow-lg text-white mt-1"
                    style={{ backgroundColor: cursor.color }}
                  >
                    {cursor.user}
                  </div>
                </motion.div>
              ))}

              {/* Collaboration Status */}
              <div className="absolute bottom-2 left-2">
                {Object.values(isTyping).some(Boolean) && (
                  <div className="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                    <div className="flex space-x-1">
                      <div className="w-1 h-1 bg-gray-400 rounded-full animate-pulse"></div>
                      <div className="w-1 h-1 bg-gray-400 rounded-full animate-pulse" style={{ animationDelay: '0.2s' }}></div>
                      <div className="w-1 h-1 bg-gray-400 rounded-full animate-pulse" style={{ animationDelay: '0.4s' }}></div>
                    </div>
                    <span>Someone is typing...</span>
                  </div>
                )}
              </div>
            </div>

            {/* Quick Actions */}
            <div className="flex items-center justify-between mt-4">
              <div className="flex items-center space-x-3">
                <button className="flex items-center space-x-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                  <PencilSquareIcon className="h-4 w-4" />
                  <span>Editing</span>
                </button>
                <button className="flex items-center space-x-2 px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm">
                  <HandRaisedIcon className="h-4 w-4" />
                  <span>Raise Hand</span>
                </button>
              </div>
              
              <div className="text-xs text-gray-500 dark:text-gray-400">
                Auto-saved just now
              </div>
            </div>
          </div>
        </div>

        {/* Chat Panel */}
        <div className="lg:col-span-1 border-l border-gray-200 dark:border-gray-700 flex flex-col">
          <div className="p-4 border-b border-gray-200 dark:border-gray-700">
            <h4 className="flex items-center text-sm font-medium text-gray-900 dark:text-white">
              <ChatBubbleLeftRightIcon className="h-4 w-4 mr-2" />
              Team Chat
            </h4>
          </div>

          {/* Messages */}
          <div className="flex-1 overflow-y-auto p-4 space-y-3">
            <AnimatePresence>
              {messages.map((message) => (
                <motion.div
                  key={message.id}
                  initial={{ opacity: 0, y: 10 }}
                  animate={{ opacity: 1, y: 0 }}
                  exit={{ opacity: 0, y: -10 }}
                  className={`${
                    message.type === 'activity' 
                      ? 'text-center text-xs text-gray-500 italic' 
                      : ''
                  }`}
                >
                  {message.type === 'activity' ? (
                    <div className="flex items-center justify-center space-x-2">
                      <DocumentTextIcon className="h-3 w-3" />
                      <span>{message.user} {message.message}</span>
                    </div>
                  ) : (
                    <div className="space-y-1">
                      <div className="flex items-center space-x-2">
                        <span className="text-xs font-medium text-gray-900 dark:text-white">
                          {message.user}
                        </span>
                        <span className="text-xs text-gray-400">
                          {new Date(message.timestamp).toLocaleTimeString()}
                        </span>
                      </div>
                      <p className="text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-900 rounded-lg p-2">
                        {message.message}
                      </p>
                    </div>
                  )}
                </motion.div>
              ))}
            </AnimatePresence>
            <div ref={messagesEndRef} />
          </div>

          {/* Message Input */}
          <div className="p-4 border-t border-gray-200 dark:border-gray-700">
            <form onSubmit={handleSendMessage} className="flex space-x-2">
              <input
                type="text"
                value={newMessage}
                onChange={(e) => setNewMessage(e.target.value)}
                placeholder="Type a message..."
                className="flex-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <button
                type="submit"
                disabled={!newMessage.trim()}
                className="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <ShareIcon className="h-4 w-4" />
              </button>
            </form>
          </div>
        </div>
      </div>

      {/* Video Call Overlay */}
      <AnimatePresence>
        {isVideoCall && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
          >
            <div className="bg-gray-900 rounded-lg p-6 max-w-4xl w-full mx-4">
              <div className="flex justify-between items-center mb-4">
                <h3 className="text-lg font-semibold text-white">Team Video Call</h3>
                <button
                  onClick={() => setIsVideoCall(false)}
                  className="text-gray-400 hover:text-white"
                >
                  ✕
                </button>
              </div>
              
              <div className="grid grid-cols-2 gap-4 mb-4">
                <div className="bg-gray-800 rounded-lg aspect-video flex items-center justify-center">
                  <div className="text-center text-white">
                    <VideoCameraIcon className="h-12 w-12 mx-auto mb-2" />
                    <p>You</p>
                  </div>
                </div>
                <div className="bg-gray-800 rounded-lg aspect-video flex items-center justify-center">
                  <div className="text-center text-white">
                    <VideoCameraIcon className="h-12 w-12 mx-auto mb-2" />
                    <p>Sarah Johnson</p>
                  </div>
                </div>
              </div>
              
              <div className="flex justify-center space-x-4">
                <button className="p-3 bg-red-600 text-white rounded-full hover:bg-red-700">
                  <VideoCameraIcon className="h-5 w-5" />
                </button>
                <button className="p-3 bg-gray-600 text-white rounded-full hover:bg-gray-700">
                  <MicrophoneIcon className="h-5 w-5" />
                </button>
                <button className="p-3 bg-gray-600 text-white rounded-full hover:bg-gray-700">
                  <ShareIcon className="h-5 w-5" />
                </button>
              </div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
};

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: "smooth" });
  };

  useEffect(scrollToBottom, [messages]);

  const handleSendMessage = (e) => {
    e.preventDefault();
    if (!newMessage.trim()) return;

    const message = {
      id: Date.now().toString(),
      user: user?.name || 'You',
      message: newMessage,
      timestamp: new Date().toISOString(),
      type: 'message'
    };

    setMessages(prev => [...prev, message]);
    setNewMessage('');

    // In production, send via WebSocket
    // wsRef.current?.send(JSON.stringify({type: 'message', data: message}));
  };

  const startVideoCall = () => {
    setIsVideoCall(!isVideoCall);
    // In production, initialize WebRTC connection
  };

  const startVoiceCall = () => {
    setIsVoiceCall(!isVoiceCall);
    // In production, initialize WebRTC audio connection
  };

  const shareCursor = (e) => {
    const rect = e.currentTarget.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    // In production, broadcast cursor position via WebSocket
    setCursors(prev => ({
      ...prev,
      [user?.id]: { x, y, user: user?.name, color: '#f59e0b' }
    }));
  };

  return (
    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
      {/* Collaboration Header */}
      <div className="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <div className="flex items-center space-x-3">
          <div className={`flex items-center space-x-2 ${isConnected ? 'text-green-600' : 'text-gray-400'}`}>
            <div className={`w-2 h-2 rounded-full ${isConnected ? 'bg-green-500' : 'bg-gray-400'} animate-pulse`}></div>
            <span className="text-sm font-medium">
              {isConnected ? 'Live Collaboration' : 'Connecting...'}
            </span>
          </div>
          
          {/* Active Users */}
          <div className="flex -space-x-2">
            <img
              src={`https://ui-avatars.io/api/?name=${encodeURIComponent(user?.name || 'You')}&background=f59e0b&color=white`}
              alt="You"
              className="w-8 h-8 rounded-full border-2 border-white dark:border-gray-700"
              title={`You (${user?.name})`}
            />
            {activeUsers.map((collaborator) => (
              <img
                key={collaborator.id}
                src={collaborator.avatar}
                alt={collaborator.name}
                className="w-8 h-8 rounded-full border-2 border-white dark:border-gray-700"
                title={`${collaborator.name} (${collaborator.status})`}
              />
            ))}
          </div>
          
          <span className="text-xs text-gray-500 dark:text-gray-400">
            {activeUsers.length + 1} online
          </span>
        </div>

        {/* Collaboration Tools */}
        <div className="flex items-center space-x-2">
          <button
            onClick={startVoiceCall}
            className={`p-2 rounded-lg transition-colors ${
              isVoiceCall 
                ? 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400' 
                : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700'
            }`}
            title="Voice Call"
          >
            <MicrophoneIcon className="h-5 w-5" />
          </button>
          
          <button
            onClick={startVideoCall}
            className={`p-2 rounded-lg transition-colors ${
              isVideoCall 
                ? 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400' 
                : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700'
            }`}
            title="Video Call"
          >
            <VideoCameraIcon className="h-5 w-5" />
          </button>
          
          <button
            className="p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
            title="Share Screen"
          >
            <ShareIcon className="h-5 w-5" />
          </button>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-4 h-96">
        {/* Main Collaboration Area */}
        <div className="lg:col-span-3 relative" onMouseMove={shareCursor}>
          <div className="p-4 h-full">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Collaborative Workspace
            </h3>
            
            {/* Document Preview/Editor Area */}
            <div className="relative bg-gray-50 dark:bg-gray-900 rounded-lg p-6 h-64 overflow-auto">
              <div className="prose dark:prose-invert max-w-none">
                <h4 className="text-base font-medium mb-3">Document: {documentType}</h4>
                <p className="text-sm text-gray-600 dark:text-gray-300">
                  This is a collaborative workspace where team members can work together in real-time.
                  Changes are synchronized instantly across all connected users.
                </p>
                <div className="mt-4 space-y-2">
                  <div className="p-2 bg-blue-50 dark:bg-blue-900/20 rounded border-l-4 border-blue-500">
                    <p className="text-sm text-blue-800 dark:text-blue-200">
                      <strong>Sarah Johnson</strong> is currently editing this section...
                    </p>
                  </div>
                  <div className="p-2 bg-green-50 dark:bg-green-900/20 rounded border-l-4 border-green-500">
                    <p className="text-sm text-green-800 dark:text-green-200">
                      <strong>Mike Chen</strong> added a comment on the AI features
                    </p>
                  </div>
                </div>
              </div>

              {/* Live Cursors */}
              {Object.entries(cursors).map(([userId, cursor]) => (
                <motion.div
                  key={userId}
                  className="absolute pointer-events-none z-10"
                  style={{ 
                    left: cursor.x, 
                    top: cursor.y, 
                    color: cursor.color 
                  }}
                  initial={{ scale: 0 }}
                  animate={{ scale: 1 }}
                  exit={{ scale: 0 }}
                >
                  <CursorArrowRaysIcon className="h-5 w-5" style={{ color: cursor.color }} />
                  <div 
                    className="text-xs font-medium px-2 py-1 rounded shadow-lg text-white mt-1"
                    style={{ backgroundColor: cursor.color }}
                  >
                    {cursor.user}
                  </div>
                </motion.div>
              ))}
            </div>

            {/* Quick Actions */}
            <div className="flex items-center justify-between mt-4">
              <div className="flex items-center space-x-3">
                <button className="flex items-center space-x-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                  <PencilSquareIcon className="h-4 w-4" />
                  <span>Start Editing</span>
                </button>
                <button className="flex items-center space-x-2 px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm">
                  <HandRaisedIcon className="h-4 w-4" />
                  <span>Raise Hand</span>
                </button>
              </div>
              
              <div className="text-xs text-gray-500 dark:text-gray-400">
                Auto-saved 2 minutes ago
              </div>
            </div>
          </div>
        </div>

        {/* Chat Panel */}
        <div className="lg:col-span-1 border-l border-gray-200 dark:border-gray-700 flex flex-col">
          <div className="p-4 border-b border-gray-200 dark:border-gray-700">
            <h4 className="flex items-center text-sm font-medium text-gray-900 dark:text-white">
              <ChatBubbleLeftRightIcon className="h-4 w-4 mr-2" />
              Team Chat
            </h4>
          </div>

          {/* Messages */}
          <div className="flex-1 overflow-y-auto p-4 space-y-3">
            <AnimatePresence>
              {messages.map((message) => (
                <motion.div
                  key={message.id}
                  initial={{ opacity: 0, y: 10 }}
                  animate={{ opacity: 1, y: 0 }}
                  exit={{ opacity: 0, y: -10 }}
                  className={`${
                    message.type === 'activity' 
                      ? 'text-center text-xs text-gray-500 italic' 
                      : ''
                  }`}
                >
                  {message.type === 'activity' ? (
                    <div className="flex items-center justify-center space-x-2">
                      <DocumentTextIcon className="h-3 w-3" />
                      <span>{message.user} {message.message}</span>
                    </div>
                  ) : (
                    <div className="space-y-1">
                      <div className="flex items-center space-x-2">
                        <span className="text-xs font-medium text-gray-900 dark:text-white">
                          {message.user}
                        </span>
                        <span className="text-xs text-gray-400">
                          {new Date(message.timestamp).toLocaleTimeString()}
                        </span>
                      </div>
                      <p className="text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-900 rounded-lg p-2">
                        {message.message}
                      </p>
                    </div>
                  )}
                </motion.div>
              ))}
            </AnimatePresence>
            <div ref={messagesEndRef} />
          </div>

          {/* Message Input */}
          <div className="p-4 border-t border-gray-200 dark:border-gray-700">
            <form onSubmit={handleSendMessage} className="flex space-x-2">
              <input
                type="text"
                value={newMessage}
                onChange={(e) => setNewMessage(e.target.value)}
                placeholder="Type a message..."
                className="flex-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <button
                type="submit"
                disabled={!newMessage.trim()}
                className="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <ShareIcon className="h-4 w-4" />
              </button>
            </form>
          </div>
        </div>
      </div>

      {/* Video Call Overlay */}
      <AnimatePresence>
        {isVideoCall && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
          >
            <div className="bg-gray-900 rounded-lg p-6 max-w-4xl w-full mx-4">
              <div className="flex justify-between items-center mb-4">
                <h3 className="text-lg font-semibold text-white">Team Video Call</h3>
                <button
                  onClick={() => setIsVideoCall(false)}
                  className="text-gray-400 hover:text-white"
                >
                  ✕
                </button>
              </div>
              
              <div className="grid grid-cols-2 gap-4 mb-4">
                <div className="bg-gray-800 rounded-lg aspect-video flex items-center justify-center">
                  <div className="text-center text-white">
                    <VideoCameraIcon className="h-12 w-12 mx-auto mb-2" />
                    <p>You</p>
                  </div>
                </div>
                <div className="bg-gray-800 rounded-lg aspect-video flex items-center justify-center">
                  <div className="text-center text-white">
                    <VideoCameraIcon className="h-12 w-12 mx-auto mb-2" />
                    <p>Sarah Johnson</p>
                  </div>
                </div>
              </div>
              
              <div className="flex justify-center space-x-4">
                <button className="p-3 bg-red-600 text-white rounded-full hover:bg-red-700">
                  <VideoCameraIcon className="h-5 w-5" />
                </button>
                <button className="p-3 bg-gray-600 text-white rounded-full hover:bg-gray-700">
                  <MicrophoneIcon className="h-5 w-5" />
                </button>
                <button className="p-3 bg-gray-600 text-white rounded-full hover:bg-gray-700">
                  <ShareIcon className="h-5 w-5" />
                </button>
              </div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
};

export default RealtimeCollaboration;