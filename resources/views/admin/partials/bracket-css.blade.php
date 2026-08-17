<style>
    /* Styling for Admin Bracket Board Canvas */
    .round-headers-bar {
        display: flex;
        background-color: #f1f5f9;
        border-bottom: 1px solid #e2e8f0;
        padding: 8px 30px;
        white-space: nowrap;
        overflow-x: hidden;
        font-size: 0.72rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .round-header-item {
        width: 185px;
        margin-right: 80px;
        flex-shrink: 0;
        text-align: center;
    }

    .bracket-container {
        padding: 30px 30px 40px 30px;
        overflow: auto;
        white-space: nowrap;
        scrollbar-width: thin;
        scrollbar-color: #e2e8f0 #ffffff;
        scroll-behavior: smooth;
        height: 620px;
        position: relative;
    }

    .bracket-round {
        display: inline-block;
        height: 4600px;
        vertical-align: top;
        width: 185px;
        margin-right: 80px;
        position: relative;
    }

    .match-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        width: 185px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
        z-index: 10;
        position: relative;
        cursor: pointer;
    }

    .match-card:hover {
        border-color: #cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .match-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        padding: 3px 6px;
        font-size: 0.6rem;
        font-weight: 800;
        color: #94a3b8;
    }

    .match-card-time {
        color: #f97316;
    }

    .team-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 24px;
        font-size: 0.72rem;
        padding-left: 6px;
        border-bottom: 1px solid #f1f5f9;
        background-color: #ffffff;
        color: #334155;
        transition: background 0.2s ease;
    }

    .team-row:last-of-type {
        border-bottom: none;
    }

    /* Styling for Drag & Drop drag states */
    .team-row[draggable="true"] {
        cursor: grab;
    }

    .team-row[draggable="true"]:active {
        cursor: grabbing;
    }

    .team-row.drag-over {
        background-color: rgba(255, 122, 0, 0.2) !important;
        outline: 1.5px dashed var(--accent-orange);
    }

    .team-info {
        display: flex;
        align-items: center;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        flex-grow: 1;
    }

    .team-name {
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .team-score-box {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.72rem;
        background-color: #f8fafc;
        color: #94a3b8;
        border-left: 1px solid #e2e8f0;
        flex-shrink: 0;
    }

    .team-row.winner {
        background-color: #f0fdf4;
    }

    .team-row.winner .team-name {
        color: #166534;
        font-weight: 700;
    }

    .team-row.winner .team-score-box {
        background-color: #22c55e;
        color: #ffffff;
        border-left-color: #22c55e;
    }

    .team-row.loser {
        opacity: 0.5;
    }

    .round-connectors {
        position: absolute;
        top: 0;
        left: 185px;
        width: 80px;
        height: 100%;
        pointer-events: none;
        z-index: 1;
    }

    .connector-line {
        fill: none;
        stroke: #cbd5e1;
        stroke-width: 1.5;
    }

    .italic {
        font-style: italic;
    }

    /* Quick Win Button Styling */
    .btn-quick-win {
        display: none;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: #f59e0b;
        color: #ffffff;
        border: none;
        font-size: 0.6rem;
        padding: 0;
        margin-right: 4px;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(245, 158, 11, 0.4);
    }
    .team-row:hover .btn-quick-win {
        display: inline-flex;
    }
    .btn-quick-win:hover {
        transform: scale(1.25);
        background-color: #d97706;
    }

    #bracketCardContainer.theme-dark .btn-quick-win {
        background-color: #ff7a00;
        color: #000000;
        box-shadow: 0 2px 6px rgba(255, 122, 0, 0.5);
    }
    #bracketCardContainer.theme-dark .btn-quick-win:hover {
        background-color: #f97316;
    }

    .round-filter-btn.active {
        box-shadow: 0 2px 6px rgba(245, 158, 11, 0.4);
    }

    /* Highlight matching cards on search */
    .match-card.search-focus-glow {
        border-color: #ff7a00 !important;
        box-shadow: 0 0 15px rgba(255, 122, 0, 0.6) !important;
        transform: scale(1.04);
        z-index: 100;
    }

    /* Team Path Highlight on Hover */
    .team-row.team-path-highlight {
        background-color: rgba(255, 122, 0, 0.25) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        box-shadow: inset 3px 0 0 #ff7a00;
    }

    .match-card.match-path-highlight {
        border-color: #ff7a00 !important;
        box-shadow: 0 0 16px rgba(255, 122, 0, 0.5) !important;
        transform: scale(1.025);
        z-index: 60 !important;
    }

    .connector-line.line-path-highlight,
    .connector-line.highlighted {
        stroke: #ff7a00 !important;
        stroke-width: 3px !important;
        filter: drop-shadow(0 0 6px rgba(255, 122, 0, 0.8));
        opacity: 1 !important;
    }

    /* Click-to-Swap Selection Highlight */
    .team-row.team-row-swap-selected {
        background-color: rgba(255, 122, 0, 0.35) !important;
        border: 2px solid #ff7a00 !important;
        box-shadow: 0 0 14px rgba(255, 122, 0, 0.9) !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        animation: swapPulse 1.2s infinite alternate ease-in-out;
    }

    @keyframes swapPulse {
        from { box-shadow: 0 0 8px rgba(255, 122, 0, 0.6); }
        to { box-shadow: 0 0 22px rgba(255, 122, 0, 1); }
    }

    @keyframes pulse {
        from { opacity: 0.6; }
        to { opacity: 1; }
    }

    .pulse-dot-admin {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 6px #10b981;
        animation: pulse-green 1.5s infinite alternate;
        display: inline-block;
    }

    @keyframes pulse-green {
        from { opacity: 0.4; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1.1); }
    }

    .bronze-match-wrapper {
        position: absolute;
        bottom: 30px;
        right: 30px;
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 50;
    }

    .bronze-match-title {
        font-size: 0.65rem;
        font-weight: 800;
        color: #f97316;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        text-align: center;
    }

    /* ---------------------------------------------------- */
    /* Theme Dark Styles for Bracket Container */
    /* ---------------------------------------------------- */
    #bracketCardContainer.theme-dark {
        background-color: #141416 !important;
        border-color: #3f3f46 !important;
    }
    #bracketCardContainer.theme-dark .round-headers-bar {
        background-color: #1e1e24 !important;
        border-bottom-color: #3f3f46 !important;
        color: #a1a1aa !important;
    }
    #bracketCardContainer.theme-dark .bracket-container {
        scrollbar-color: #ff7a00 #141416;
    }
    #bracketCardContainer.theme-dark .bracket-container::-webkit-scrollbar-track {
        background: #141416;
    }
    #bracketCardContainer.theme-dark .bracket-container::-webkit-scrollbar-thumb {
        background: #3f3f46;
        border-radius: 3px;
    }
    #bracketCardContainer.theme-dark .bracket-container::-webkit-scrollbar-thumb:hover {
        background: #ff7a00;
    }
    #bracketCardContainer.theme-dark .match-card {
        background-color: #2d2d35 !important;
        border-color: #3f3f46 !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
    }
    #bracketCardContainer.theme-dark .match-card:hover {
        border-color: #52525b !important;
    }
    #bracketCardContainer.theme-dark .match-card-header {
        background-color: #202024 !important;
        border-bottom-color: #3f3f46 !important;
        color: #a1a1aa !important;
    }
    #bracketCardContainer.theme-dark .match-card-header .match-card-time {
        color: #ff7a00 !important;
    }
    #bracketCardContainer.theme-dark .team-row {
        background-color: #2d2d35 !important;
        border-bottom-color: rgba(255, 255, 255, 0.03) !important;
        color: #f4f4f5 !important;
    }
    #bracketCardContainer.theme-dark .team-row:hover {
        background-color: #373740 !important;
    }
    #bracketCardContainer.theme-dark .team-seed {
        color: #a1a1aa !important;
    }
    #bracketCardContainer.theme-dark .team-name {
        color: #f4f4f5 !important;
    }
    #bracketCardContainer.theme-dark .team-name.text-muted {
        color: #3f3f46 !important;
        opacity: 0.3;
    }
    #bracketCardContainer.theme-dark .team-score-box {
        background-color: #202024 !important;
        border-left-color: #3f3f46 !important;
        color: #a1a1aa !important;
    }
    #bracketCardContainer.theme-dark .team-row.winner {
        background-color: rgba(255, 122, 0, 0.02) !important;
    }
    #bracketCardContainer.theme-dark .team-row.winner .team-name {
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    #bracketCardContainer.theme-dark .team-row.winner .team-score-box {
        background-color: #ff7a00 !important;
        color: #000000 !important;
    }
    #bracketCardContainer.theme-dark .team-row.loser {
        opacity: 0.45;
        background-color: transparent !important;
    }
    #bracketCardContainer.theme-dark .team-row.loser .team-name {
        color: #f4f4f5 !important;
    }
    #bracketCardContainer.theme-dark .round-connectors path {
        stroke: #44444f !important;
        stroke-width: 1.5;
    }
    #bracketCardContainer.theme-dark .bronze-match-title {
        color: #ff7a00 !important;
    }
</style>
