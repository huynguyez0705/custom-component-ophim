(function () {
    const popupLinkData = window.popupLinkData || {};
    const priorityLinks = popupLinkData.priority_links || [];
    const normalLinks = popupLinkData.links || [];
    const mode = popupLinkData.mode || "sequential";
    const waitTime = (isNaN(parseFloat(popupLinkData.replay_time)) ? 10 : parseFloat(popupLinkData.replay_time)) * 60000;
    const priorityDelay = (isNaN(parseFloat(popupLinkData.priority_time)) ? 1 : parseFloat(popupLinkData.priority_time)) * 60000;


    const get = key => {
        try {
            return JSON.parse(sessionStorage.getItem(key));
        } catch {
            return null;
        }
    };
    const set = (key, value) => {
        try {
            sessionStorage.setItem(key, JSON.stringify(value));
        } catch (e) {
            console.error("Lỗi khi lưu vào sessionStorage:", e);
        }
    };

    if (!get("pageVisitedTime")) set("pageVisitedTime", Date.now());
    if (!get("priorityClicks")) set("priorityClicks", 0);
    if (!get("normalClicks")) set("normalClicks", 0);
    if (!get("lastOpenedNormalIndex")) set("lastOpenedNormalIndex", 0);
    if (!get("lastOpenedPriorityIndex")) set("lastOpenedPriorityIndex", 0);
    if (!get("popupUsedNormalLinks")) set("popupUsedNormalLinks", []);
    if (!get("popupUsedPriorityLinks")) set("popupUsedPriorityLinks", []);
    if (!get("normalStartTime")) set("normalStartTime", null);
    if (!get("priorityCompletedTime")) set("priorityCompletedTime", null);

    const isMobileDevice = () =>
        /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
        window.matchMedia("(max-width: 768px)").matches;

    const getPriorityLink = () => {
        if (!priorityLinks.length) return null;
        if (mode === "random") {
            let used = Array.isArray(get("popupUsedPriorityLinks")) ? get("popupUsedPriorityLinks") : [];
            if (used.length >= priorityLinks.length) used = [];
            const unused = priorityLinks.map((_, i) => i).filter(i => !used.includes(i));
            if (!unused.length) {
                used = [];
                set("popupUsedPriorityLinks", used);
            }
            const idx = unused[Math.floor(Math.random() * unused.length)];
            used.push(idx);
            set("popupUsedPriorityLinks", used);
            return priorityLinks[idx];
        } else {
            let idx = parseInt(get("lastOpenedPriorityIndex")) || 0;
            const link = priorityLinks[idx % priorityLinks.length];
            set("lastOpenedPriorityIndex", idx + 1);
            return link;
        }
    };

    const getNormalLink = () => {
        if (!normalLinks.length) return null;
        if (mode === "random") {
            let used = Array.isArray(get("popupUsedNormalLinks")) ? get("popupUsedNormalLinks") : [];
            if (used.length >= normalLinks.length) used = [];
            const unused = normalLinks.map((_, i) => i).filter(i => !used.includes(i));
            if (!unused.length) {
                used = [];
                set("popupUsedNormalLinks", used);
            }
            const idx = unused[Math.floor(Math.random() * unused.length)];
            used.push(idx);
            set("popupUsedNormalLinks", used);
            return normalLinks[idx];
        } else {
            let idx = parseInt(get("lastOpenedNormalIndex")) || 0;
            const link = normalLinks[idx % normalLinks.length];
            set("lastOpenedNormalIndex", idx + 1);
            return link;
        }
    };

    const priorityAllowed = () => {
        if (!priorityLinks.length) return false;

        const pc = parseInt(get("priorityClicks")) || 0;
        if (pc >= 1) return false;

        const pageVisitedTime = parseInt(get("pageVisitedTime")) || Date.now();
        const timePassed = Date.now() - pageVisitedTime;

        if (timePassed < priorityDelay) return false;

        if (timePassed >= (waitTime - 60000)) return false;

        return true;
    };


    const normalAllowed = () => {
        if (!normalLinks.length) return false;

        const now = Date.now();
        let nc = parseInt(get("normalClicks")) || 0;
        const pageVisitedTime = parseInt(get("pageVisitedTime")) || now;
        const timePassed = now - pageVisitedTime;
        let normalStartTime = parseInt(get("normalStartTime")) || null;

        const isReadyForNormal = timePassed >= waitTime;

        if (!isReadyForNormal && !priorityAllowed()) return false;

        if (normalStartTime === null) {
            normalStartTime = now;
            nc = 0;
            set("normalStartTime", normalStartTime);
            set("normalClicks", nc);
        }

        const timeSinceNormalStart = now - normalStartTime;
        if (timeSinceNormalStart >= waitTime) {
            normalStartTime = now;
            nc = 0;
            set("normalStartTime", normalStartTime);
            set("normalClicks", nc);
        }

        return nc < 2;
    };

    const openLink = url => {
        if (!url || typeof url !== 'string') return;
        const newWindow = window.open(url, "_blank", "noopener,noreferrer");
        if (!newWindow) {
            console.warn("Không thể mở cửa sổ mới, có thể do trình duyệt chặn popup.");
        }
    };

    document.addEventListener("click", () => {
        if (!isMobileDevice()) return;
        if (!priorityLinks.length && !normalLinks.length) return;

        const pageVisitedTime = parseInt(get("pageVisitedTime")) || Date.now();
        const timePassed = Date.now() - pageVisitedTime;

        if (timePassed >= waitTime) {
            if (normalAllowed()) {
                const link = getNormalLink();
                if (link) {
                    openLink(link);
                    set("normalClicks", (parseInt(get("normalClicks")) || 0) + 1);
                }
            }
            return;
        }

        if (priorityAllowed()) {
            const link = getPriorityLink();
            if (link) {
                openLink(link);
                const oldPc = parseInt(get("priorityClicks")) || 0;
                const newPc = oldPc + 1;
                set("priorityClicks", newPc);
                if (newPc >= 1 && get("priorityCompletedTime") === null) {
                    set("priorityCompletedTime", Date.now());
                }
            }
            return;
        }

        if (normalAllowed()) {
            const link = getNormalLink();
            if (link) {
                openLink(link);
                set("normalClicks", (parseInt(get("normalClicks")) || 0) + 1);
            }
        }
    }, false);
})();