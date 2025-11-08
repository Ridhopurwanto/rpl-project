import React, { useState, useRef, useEffect } from 'react';
import { Camera, X, ChevronDown, ChevronUp, ChevronLeft, ChevronRight, ImageIcon } from 'lucide-react';

function Presensi({ username, onBack }) {
  const [currentDate, setCurrentDate] = useState(new Date());
  const [selectedMonth, setSelectedMonth] = useState(currentDate.getMonth());
  const [selectedYear, setSelectedYear] = useState(currentDate.getFullYear());
  const [selectedShift, setSelectedShift] = useState('ALL');
  const [showHistory, setShowHistory] = useState(true);
  const [showCamera, setShowCamera] = useState(false);
  const [presensiType, setPresensiType] = useState(null);
  const [capturedPhoto, setCapturedPhoto] = useState(null);
  const [showPhotoModal, setShowPhotoModal] = useState(false);
  const [selectedPhoto, setSelectedPhoto] = useState(null);
  const videoRef = useRef(null);
  const canvasRef = useRef(null);

  // Generate shift data for any month (cyclical pattern)
  const generateShiftData = (month, year) => {
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const shifts = {};
    const pattern = ['pagi', 'pagi', 'malam', 'malam', 'malam', 'off', 'off'];
    
    for (let day = 1; day <= daysInMonth; day++) {
      shifts[day] = pattern[(day - 1) % pattern.length];
    }
    return shifts;
  };

  const shiftData = generateShiftData(selectedMonth, selectedYear);

  // Dummy riwayat presensi
  const [presensiHistory] = useState([
    {
      date: '06/11/2025',
      shift: 'SHIFT PAGI',
      datang: { time: '06:55:35', photo: 'photo1.jpg', location: 'JL. OTTO ISKANDARDINATA' },
      pulang: null
    },
    {
      date: '05/11/2025',
      shift: 'SHIFT MALAM',
      datang: { time: '17:34:06', photo: 'photo2.jpg', location: 'PULO, KEBAYORAN BARU, DKI JAKARTA SELATAN' },
      pulang: { time: '05:12:15', photo: 'photo3.jpg', location: 'PULO, KEBAYORAN BARU' }
    }
  ]);

  const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  
  const getDaysInMonth = (month, year) => {
    return new Date(year, month + 1, 0).getDate();
  };

  const getFirstDayOfMonth = (month, year) => {
    return new Date(year, month, 1).getDay();
  };

  const goToPreviousMonth = () => {
    if (selectedMonth === 0) {
      setSelectedMonth(11);
      setSelectedYear(selectedYear - 1);
    } else {
      setSelectedMonth(selectedMonth - 1);
    }
  };

  const goToNextMonth = () => {
    if (selectedMonth === 11) {
      setSelectedMonth(0);
      setSelectedYear(selectedYear + 1);
    } else {
      setSelectedMonth(selectedMonth + 1);
    }
  };

  const openCamera = async (type) => {
    setPresensiType(type);
    setShowCamera(true);
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ 
        video: { facingMode: 'user' } 
      });
      if (videoRef.current) {
        videoRef.current.srcObject = stream;
      }
    } catch (err) {
      alert('Tidak dapat mengakses kamera');
    }
  };

  const capturePhoto = () => {
    const canvas = canvasRef.current;
    const video = videoRef.current;
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    const photoData = canvas.toDataURL('image/jpeg');
    setCapturedPhoto(photoData);
    closeCamera();
    alert(`Presensi ${presensiType} berhasil!`);
  };

  const closeCamera = () => {
    if (videoRef.current && videoRef.current.srcObject) {
      videoRef.current.srcObject.getTracks().forEach(track => track.stop());
    }
    setShowCamera(false);
  };

  const renderCalendar = () => {
    const daysInMonth = getDaysInMonth(selectedMonth, selectedYear);
    const firstDay = getFirstDayOfMonth(selectedMonth, selectedYear);
    const days = [];
    
    // Empty cells before first day
    for (let i = 0; i < firstDay; i++) {
      days.push(<div key={`empty-${i}`} className="aspect-square"></div>);
    }
    
    // Days of month
    for (let day = 1; day <= daysInMonth; day++) {
      const shift = shiftData[day];
      let bgColor = 'bg-gray-100';
      if (shift === 'pagi') bgColor = 'bg-yellow-300';
      else if (shift === 'malam') bgColor = 'bg-cyan-300';
      else if (shift === 'off') bgColor = 'bg-red-500 text-white';
      
      const isToday = day === currentDate.getDate() && 
                      selectedMonth === currentDate.getMonth() && 
                      selectedYear === currentDate.getFullYear();
      
      days.push(
        <div 
          key={day} 
          className={`aspect-square flex items-center justify-center text-xs md:text-sm lg:text-base font-semibold ${bgColor} rounded-lg ${isToday ? 'ring-2 ring-blue-900 ring-offset-2' : ''}`}
        >
          {day}
        </div>
      );
    }
    
    return days;
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900 p-3 md:p-4 lg:p-6 xl:p-8">
      <div className="w-full max-w-md lg:max-w-3xl xl:max-w-5xl mx-auto">
        
        {/* Header */}
        <div className="bg-gradient-to-r from-blue-800 to-blue-900 rounded-t-3xl p-4 md:p-5 lg:p-7 xl:p-8 mb-4 md:mb-5">
          <div className="flex items-center gap-3 md:gap-4">
            <button 
              onClick={onBack}
              className="w-10 h-10 md:w-12 md:h-12 lg:w-14 lg:h-14 bg-white/20 rounded-lg flex items-center justify-center hover:bg-white/30 transition"
            >
              <span className="text-white text-xl md:text-2xl lg:text-3xl">←</span>
            </button>
            <h1 className="text-white text-xl md:text-2xl lg:text-3xl xl:text-4xl font-bold flex-1">PRESENSI</h1>
          </div>
        </div>

        {/* Main Content */}
        <div className="bg-white rounded-3xl shadow-2xl p-4 md:p-5 lg:p-7 xl:p-10">
          
          {/* Month/Year Display with Navigation */}
          <div className="flex items-center justify-between mb-4 md:mb-5 lg:mb-6">
            <button 
              onClick={goToPreviousMonth}
              className="w-8 h-8 md:w-10 md:h-10 lg:w-12 lg:h-12 bg-blue-100 hover:bg-blue-200 rounded-lg flex items-center justify-center transition"
            >
              <ChevronLeft className="text-blue-900" size={20} />
            </button>
            
            <h2 className="text-lg md:text-xl lg:text-2xl xl:text-3xl font-bold text-gray-800">
              {monthNames[selectedMonth].toUpperCase()} {selectedYear}
            </h2>
            
            <button 
              onClick={goToNextMonth}
              className="w-8 h-8 md:w-10 md:h-10 lg:w-12 lg:h-12 bg-blue-100 hover:bg-blue-200 rounded-lg flex items-center justify-center transition"
            >
              <ChevronRight className="text-blue-900" size={20} />
            </button>
          </div>

          {/* Calendar */}
          <div className="mb-4 md:mb-5 lg:mb-6">
            <div className="grid grid-cols-7 gap-1 md:gap-2 lg:gap-3 mb-2">
              {['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'].map(day => (
                <div key={day} className="text-center text-xs md:text-sm lg:text-base font-semibold text-gray-600">
                  {day}
                </div>
              ))}
            </div>
            <div className="grid grid-cols-7 gap-1 md:gap-2 lg:gap-3">
              {renderCalendar()}
            </div>
          </div>

          {/* Legend */}
          <div className="flex gap-2 md:gap-4 lg:gap-6 justify-center mb-4 md:mb-5 lg:mb-6 flex-wrap text-xs md:text-sm lg:text-base">
            <div className="flex items-center gap-1 md:gap-2">
              <div className="w-4 h-4 md:w-5 md:h-5 lg:w-6 lg:h-6 bg-yellow-300 rounded"></div>
              <span className="font-semibold">Shift Pagi</span>
            </div>
            <div className="flex items-center gap-1 md:gap-2">
              <div className="w-4 h-4 md:w-5 md:h-5 lg:w-6 lg:h-6 bg-cyan-300 rounded"></div>
              <span className="font-semibold">Shift Malam</span>
            </div>
            <div className="flex items-center gap-1 md:gap-2">
              <div className="w-4 h-4 md:w-5 md:h-5 lg:w-6 lg:h-6 bg-red-500 rounded"></div>
              <span className="font-semibold">Off</span>
            </div>
          </div>

          {/* History Section */}
          <div className="border-t-2 pt-4 md:pt-5 lg:pt-6">
            <button 
              onClick={() => setShowHistory(!showHistory)}
              className="flex items-center justify-between w-full mb-3 md:mb-4 hover:bg-gray-50 p-2 rounded-lg transition"
            >
              <div className="flex items-center gap-2 md:gap-3 flex-wrap">
                <span className="text-sm md:text-base lg:text-lg xl:text-xl font-bold text-blue-900">RIWAYAT :</span>
                <select 
                  className="px-2 py-1 md:px-3 md:py-2 lg:px-4 lg:py-2 bg-blue-900 text-white rounded-lg text-xs md:text-sm lg:text-base font-semibold"
                  value={selectedShift}
                  onChange={(e) => setSelectedShift(e.target.value)}
                  onClick={(e) => e.stopPropagation()}
                >
                  <option value="ALL">SEMUA SHIFT</option>
                  <option value="PAGI">SHIFT PAGI</option>
                  <option value="MALAM">SHIFT MALAM</option>
                </select>
              </div>
              <div className="text-blue-900">
                {showHistory ? <ChevronUp size={24} /> : <ChevronDown size={24} />}
              </div>
            </button>

            {showHistory && (
              <div className="space-y-3 md:space-y-4">
                {/* Table Header */}
                <div className="grid grid-cols-4 gap-2 md:gap-3 lg:gap-4 bg-blue-900 text-white p-2 md:p-3 lg:p-4 rounded-lg text-xs md:text-sm lg:text-base font-bold">
                  <div className="text-center">Foto</div>
                  <div className="text-center">Waktu</div>
                  <div className="text-center">Tempat</div>
                  <div className="text-center">Status</div>
                </div>

                {/* History Items */}
                {presensiHistory.map((item, idx) => (
                  <div key={idx} className="border-2 rounded-xl p-3 md:p-4 lg:p-5 hover:shadow-md transition">
                    <div className="flex items-center justify-between mb-3">
                      <span className="text-xs md:text-sm lg:text-base font-bold text-gray-600">
                        TANGGAL: {item.date}
                      </span>
                      <span className="text-xs md:text-sm lg:text-base font-bold text-blue-900">
                        {item.shift}
                      </span>
                    </div>

                    {/* Datang */}
                    {item.datang && (
                      <div className="grid grid-cols-4 gap-2 md:gap-3 lg:gap-4 items-center mb-2 text-xs md:text-sm lg:text-base">
                        <button 
                          onClick={() => {
                            setSelectedPhoto(item.datang.photo);
                            setShowPhotoModal(true);
                          }}
                          className="flex justify-center hover:scale-110 transition"
                        >
                          <ImageIcon className="w-6 h-6 md:w-8 md:h-8 lg:w-10 lg:h-10 text-blue-600" />
                        </button>
                        <div className="text-center font-semibold">{item.datang.time}</div>
                        <div className="text-center text-xs md:text-sm">{item.datang.location}</div>
                        <div className="text-center">
                          <span className="bg-green-500 text-white px-2 py-1 rounded text-xs md:text-sm font-bold inline-block">
                            Tepat Waktu
                          </span>
                        </div>
                      </div>
                    )}

                    {/* Pulang */}
                    {item.pulang && (
                      <div className="grid grid-cols-4 gap-2 md:gap-3 lg:gap-4 items-center text-xs md:text-sm lg:text-base">
                        <button 
                          onClick={() => {
                            setSelectedPhoto(item.pulang.photo);
                            setShowPhotoModal(true);
                          }}
                          className="flex justify-center hover:scale-110 transition"
                        >
                          <ImageIcon className="w-6 h-6 md:w-8 md:h-8 lg:w-10 lg:h-10 text-blue-600" />
                        </button>
                        <div className="text-center font-semibold">{item.pulang.time}</div>
                        <div className="text-center text-xs md:text-sm">{item.pulang.location}</div>
                        <div className="text-center">
                          <span className="bg-green-500 text-white px-2 py-1 rounded text-xs md:text-sm font-bold inline-block">
                            Tepat Waktu
                          </span>
                        </div>
                      </div>
                    )}
                  </div>
                ))}
              </div>
            )}
          </div>

          {/* Floating Action Button */}
          <div className="fixed bottom-6 right-6 md:bottom-8 md:right-8 lg:bottom-10 lg:right-10 flex flex-col gap-3">
            <button 
              onClick={() => openCamera('datang')}
              className="w-14 h-14 md:w-16 md:h-16 lg:w-20 lg:h-20 xl:w-24 xl:h-24 bg-gradient-to-r from-blue-800 to-blue-900 rounded-full shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-transform"
            >
              <span className="text-3xl md:text-4xl lg:text-5xl text-white">+</span>
            </button>
          </div>
        </div>

        {/* Camera Modal */}
        {showCamera && (
          <div className="fixed inset-0 bg-black/90 flex items-center justify-center z-50 p-4">
            <div className="bg-white rounded-2xl max-w-lg lg:max-w-2xl w-full p-4 md:p-6 lg:p-8">
              <div className="flex justify-between items-center mb-4">
                <h3 className="text-lg md:text-xl lg:text-2xl font-bold text-gray-800">
                  Presensi {presensiType === 'datang' ? 'Datang' : 'Pulang'}
                </h3>
                <button onClick={closeCamera} className="text-gray-500 hover:text-gray-700">
                  <X size={24} />
                </button>
              </div>
              
              <video 
                ref={videoRef} 
                autoPlay 
                playsInline
                className="w-full rounded-lg mb-4"
              />
              <canvas ref={canvasRef} className="hidden" />
              
              <button 
                onClick={capturePhoto}
                className="w-full bg-gradient-to-r from-blue-800 to-blue-900 text-white font-bold py-3 md:py-4 lg:py-5 rounded-full hover:from-blue-700 hover:to-blue-800 transition flex items-center justify-center gap-2 text-base md:text-lg"
              >
                <Camera size={20} />
                Ambil Foto
              </button>
            </div>
          </div>
        )}

        {/* Photo View Modal */}
        {showPhotoModal && (
          <div className="fixed inset-0 bg-black/90 flex items-center justify-center z-50 p-4">
            <div className="bg-white rounded-2xl max-w-lg lg:max-w-2xl w-full p-4 md:p-6 lg:p-8">
              <div className="flex justify-between items-center mb-4">
                <h3 className="text-lg md:text-xl lg:text-2xl font-bold text-gray-800">Foto Presensi</h3>
                <button 
                  onClick={() => setShowPhotoModal(false)} 
                  className="text-gray-500 hover:text-gray-700"
                >
                  <X size={24} />
                </button>
              </div>
              
              <div className="bg-gray-200 rounded-lg aspect-video flex items-center justify-center">
                <ImageIcon size={64} className="text-gray-400" />
              </div>
              
              <p className="text-center text-sm md:text-base text-gray-600 mt-4">
                Preview foto presensi
              </p>
            </div>
          </div>
        )}

      </div>
    </div>
  );
}

export default Presensi;