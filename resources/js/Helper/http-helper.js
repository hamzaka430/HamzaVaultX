import { usePage } from "@inertiajs/vue3";

export const httpGet = (url) => {
    return fetch(url, {
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
        },
    }).then((response) => {
        return response.json();
    });
};

export const httpPost = (url, data) => {
    return new Promise((resolve, reject) => {
        // Get CSRF token from Inertia props or meta tag as fallback
        const csrfToken = usePage()?.props?._csrf_token || 
                         document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        fetch(url, {
            method: "POST",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify(data),
        }).then((response) => {
            if (response.ok) {
                return resolve(response.json());
            } else {
                return response.json().then((data) => {
                    reject({ response, error: data });
                });
            }
        });
    });
};
