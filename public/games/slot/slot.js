import { themes } from "./themes.js";

const params = new URLSearchParams(window.location.search);
const theme = themes[params.get("theme")] || themes["lucky-sevens"];

document.documentElement.style.setProperty("--from", theme.from);
document.documentElement.style.setProperty("--to", theme.to);
document.documentElement.style.setProperty("--accent", theme.accent);
document.getElementById("title").textContent = theme.name;

const cache = {};
const START_CREDITS = 500;
const BET = 10;

class SymbolIcon {
  constructor(name = SymbolIcon.random()) {
    this.name = name;

    if (cache[name]) {
      this.img = cache[name].cloneNode();
    } else {
      this.img = new Image();
      this.img.src = `./symbols/${name}.png`;
      this.img.alt = name;
      cache[name] = this.img;
    }
  }

  static get symbols() {
    return theme.symbols;
  }

  static random() {
    return this.symbols[Math.floor(Math.random() * this.symbols.length)];
  }

  static preload() {
    return Promise.all(
      SymbolIcon.symbols.map((name) => {
        if (cache[name] && cache[name].complete) {
          return Promise.resolve();
        }
        const img = new Image();
        img.src = `./symbols/${name}.png`;
        img.alt = name;
        cache[name] = img;
        return img.decode().catch(() => {});
      }),
    );
  }
}

class Reel {
  constructor(reelContainer, idx, initialSymbols) {
    this.reelContainer = reelContainer;
    this.idx = idx;
    this.busy = false;

    this.symbolContainer = document.createElement("div");
    this.symbolContainer.classList.add("icons");
    this.reelContainer.appendChild(this.symbolContainer);

    initialSymbols.forEach((symbol) => {
      this.symbolContainer.appendChild(new SymbolIcon(symbol).img);
    });
    this.applySizes();
  }

  get extraCount() {
    return 14 + this.idx * 5;
  }

  symbolHeight() {
    return this.reelContainer.clientHeight / 3 || 120;
  }

  applySizes() {
    const height = this.symbolHeight();
    Array.from(this.symbolContainer.children).forEach((img) => {
      img.style.height = `${height}px`;
      img.style.width = "100%";
      img.style.objectFit = "contain";
      img.style.margin = "0";
      img.style.display = "block";
    });
  }

  renderSymbols(nextSymbols) {
    const fragment = document.createDocumentFragment();
    for (let i = 0; i < this.extraCount; i += 1) {
      fragment.appendChild(new SymbolIcon().img);
    }
    nextSymbols.forEach((name) => {
      fragment.appendChild(new SymbolIcon(name).img);
    });
    this.symbolContainer.appendChild(fragment);
    this.applySizes();
  }

  spin() {
    if (this.busy) {
      return Promise.resolve();
    }
    this.busy = true;
    this.applySizes();

    const height = this.symbolHeight();
    const extra = this.extraCount;
    const distance = (3 + extra) * height;
    const duration = 1100 + this.idx * 260;

    this.symbolContainer.style.transition = "none";
    this.symbolContainer.style.transform = "translate3d(0, 0, 0)";
    void this.symbolContainer.offsetHeight;

    this.symbolContainer.style.transition = `transform ${duration}ms cubic-bezier(0.12, 0.7, 0.12, 1)`;
    this.symbolContainer.style.transform = `translate3d(0, ${-distance}px, 0)`;

    return new Promise((resolve) => {
      let settled = false;
      const finish = () => {
        if (settled) return;
        settled = true;
        this.symbolContainer.removeEventListener("transitionend", onEnd);
        while (this.symbolContainer.children.length > 3) {
          this.symbolContainer.firstChild.remove();
        }
        this.symbolContainer.style.transition = "none";
        this.symbolContainer.style.transform = "translate3d(0, 0, 0)";
        this.applySizes();
        this.busy = false;
        resolve();
      };
      const onEnd = (event) => {
        if (event.target === this.symbolContainer) {
          finish();
        }
      };
      this.symbolContainer.addEventListener("transitionend", onEnd);
      window.setTimeout(finish, duration + 80);
    });
  }
}

class Slot {
  constructor(domElement) {
    const first = theme.symbols[0];
    this.currentSymbols = [
      [first, first, first],
      [first, first, first],
      [first, first, first],
      [first, first, first],
      [first, first, first],
    ];
    this.nextSymbols = this.currentSymbols;
    this.credits = START_CREDITS;

    this.container = domElement;
    this.reels = Array.from(this.container.getElementsByClassName("reel")).map(
      (reelContainer, idx) =>
        new Reel(reelContainer, idx, this.currentSymbols[idx]),
    );

    this.spinButton = document.getElementById("spin");
    this.spinButton.addEventListener("click", () => this.spin());
    this.autoPlayCheckbox = document.getElementById("autoplay");
    this.status = document.getElementById("status");
    this.creditValue = document.getElementById("credit-value");

    const size = () => {
      this.reels.forEach((reel) => reel.applySizes());
    };
    window.addEventListener("resize", size);
    window.requestAnimationFrame(size);
  }

  randomRow() {
    return [SymbolIcon.random(), SymbolIcon.random(), SymbolIcon.random()];
  }

  spin() {
    if (this.spinButton.disabled) return;
    if (this.credits < BET) {
      this.status.textContent = "Out of virtual credits. Refresh to refill.";
      this.autoPlayCheckbox.checked = false;
      return;
    }

    this.credits -= BET;
    this.creditValue.textContent = String(this.credits);
    this.currentSymbols = this.nextSymbols;
    this.nextSymbols = [
      this.randomRow(),
      this.randomRow(),
      this.randomRow(),
      this.randomRow(),
      this.randomRow(),
    ];

    this.spinButton.disabled = true;
    this.status.textContent = "Good luck...";

    this.reels.forEach((reel) => {
      reel.renderSymbols(this.nextSymbols[reel.idx]);
    });

    return Promise.all(this.reels.map((reel) => reel.spin())).then(() =>
      this.onSpinEnd(),
    );
  }

  onSpinEnd() {
    const middle = this.nextSymbols.map((reel) => reel[1]);
    let match = 1;
    for (let i = 1; i < middle.length; i += 1) {
      if (middle[i] === middle[0]) match += 1;
      else break;
    }

    let win = 0;
    if (match >= 5) win = BET * 20;
    else if (match >= 4) win = BET * 10;
    else if (match >= 3) win = BET * 5;

    if (win > 0) {
      this.credits += win;
      this.creditValue.textContent = String(this.credits);
      this.status.textContent = `Nice line! +${win} virtual credits.`;
    } else {
      this.status.textContent = "No line that time. Spin again.";
    }

    this.spinButton.disabled = false;

    if (this.autoPlayCheckbox.checked) {
      window.setTimeout(() => this.spin(), 500);
    }
  }
}

SymbolIcon.preload().then(() => {
  new Slot(document.getElementById("slot"));
});
