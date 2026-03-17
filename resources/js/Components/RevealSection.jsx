import React, { useState, useEffect, useRef } from 'react';

export default function RevealSection({ children, className = "", delay = "0ms", direction = "up" }) {
    const [isVisible, setIsVisible] = useState(false);
    const domRef = useRef();

    useEffect(() => {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    setIsVisible(true);
                    // observer.unobserve(entry.target); // Uncomment if we only want it to reveal once
                }
            });
        }, { threshold: 0.1 });

        const { current } = domRef;
        if (current) observer.observe(current);

        return () => {
            if (current) observer.unobserve(current);
        };
    }, []);

    const directionClasses = {
        up: 'translate-y-10',
        down: '-translate-y-10',
        left: 'translate-x-10',
        right: '-translate-x-10',
        none: 'scale-95'
    };

    return (
        <div
            ref={domRef}
            style={{ transitionDelay: delay }}
            className={`${className} transition-all duration-1000 transform ${isVisible ? 'opacity-100 translate-y-0 translate-x-0 scale-100' : `opacity-0 ${directionClasses[direction] || ''}`}`}
        >
            {children}
        </div>
    );
}